<?php
use Illuminate\Support\Facades\File as FileSys;
use App\myLibs\ID3TagsReader  as ID3Tags;
use App\myLibs\EXIFTagsReader as EXIFTags;
use App\myLibs\M4ATagsReader  as M4ATags;
use App\myLibs\GPXTagsReader  as GPXTags;
use PHPCoord\OSRef;
//use App\Album;
//use App\Category;
//use App\Comment;
use App\File;
use App\Folder;
//use App\Permission;
//use App\Photo;
//use App\Post;
//use App\Profile;
//use App\Role;
//use App\Tag;
use App\User;

// Simple trace routine
if (! function_exists('trace')) {
    function trace($data='_ _ _ _ _ _ _ _ _') {
        echo var_export($data, true) . '<br>';
    }
}    

// System wide constants set here because .env and config() don't seem to work very well
// especially if cache is active.
if (! function_exists('myConstants')) {
    function myConstants($id=false) {
        # Google recaptcha account details used within public form submission
        $CAPTCHA_SITEKEY = '6LfjL18UAAAAAJndLVImcv5hfMo3P3TV9o7puzH9';
        $CAPTCHA_SERVER  = 'https://www.google.com/recaptcha/api/siteverify';
        $CAPTCHA_SECRET  = '6LfjL18UAAAAAMRoQFB3k2iQfPiS8XmC5zKWNMXO';

        # Ordnance Survey OpenSpace API key
        $OS_APIKEY = '74BBA293ABAA3E78E0530C6CA40A9F3F';
        return $id ? (isset($$id) ? $$id : false) : false;
    }
}    

// Inserts one or more objects into an existsing object at a specified position.
if (! function_exists('insertObject')) {
    function insertObject($target=[], $offset=0, $insert=[]) {
        $result = array_slice($target, 0, $offset);
        if (gettype($insert) != 'array' ) { $insert = array($insert); }
        foreach ($insert as $item) {
            $result[] = $item;
        }  
        $insert = array_slice($target, $offset);
        foreach ($insert as $item) {
            $result[] = $item;
        }      
        return $result;
    }
}    

// This function returns a list of OSRef objects with calulated distances and elevations.
// The list may optionally be filled in with intermediate points.   
// Returns: {OSRef objects}
// Input  : {{easting, northing}...{easting, northing}}, [distances], [elevations]
// If distances = true then calculate distances, if integer then build inters and calc distances
// NS01
if (! function_exists('OSRefs')) {
    function OSRefs($xyzs=[], $distances=true, $elevations=false) {
        $OSRefs=[];
        foreach ($xyzs as $xyz) {                                   // Create an OSRef for each point
            $OSRefs[] = OSRef($xyz, $elevations);                   // ... with optional Elevation
        }

        for ($i=0; $i<count($OSRefs); ++$i) {
            if (gettype($distances)=='integer' && $i>0) {           // Expand list with intermediate points
                $d = $OSRefs[$i-1]->distance($OSRefs[$i]);
                if ($d>$distances) {                                // ... until we are close 
                    $x = $OSRefs[$i]->getX() - $OSRefs[$i -1]->getX();
                    $y = $OSRefs[$i]->getY() - $OSRefs[$i -1]->getY();

                    $radAngle = $x==0 ? pi()/2 : atan($y/$x);       // NS01
                    if ($x<0) { $sign = -1; } else { $sign = 1;} 
                    $xInt = $OSRefs[$i-1]->getX() + ($distances * cos($radAngle) * $sign);
                    $yInt = $OSRefs[$i-1]->getY() + ($distances * sin($radAngle) * $sign);

                    $OSRef = OSRef([$xInt, $yInt], $elevations);    // Make intermediate here
                    $OSRefs = insertObject($OSRefs, $i, $OSRef);    // .. and insert into our list

                    if (count($OSRefs)>=9999) { break; }            // Maximum reached (500km)!
                }

            }    
        }    

        if ($distances!=false) {                                    // Add distance values between each point
            for ($i=1; $i<count($OSRefs); ++$i) {
                $OSRefs[$i]->d = round($OSRefs[$i-1]->distance($OSRefs[$i]), 1);
            }
        }
        return $OSRefs;
    }
}    
   
// Returns: OSRef object and optionally does an Elevation lookup
// Input  : {Easting, Northing, [Elevation]}, [LookupElevation]
if (! function_exists('OSRef')) {
    function OSRef($xyz=[], $lookupElevation=false) {
        $myrc = false;
        if (count($xyz)>=2) {
            if ($lookupElevation) {
                $xyz[2]=getElevation($xyz);
            } elseif (count($xyz)==2) {
                $xyz[2] = 0;
            }
            $myrc = new OSRef($xyz[0], $xyz[1], $xyz[2]);           // Easting, Northing, [Elevation]
        }
        return $myrc;
    }
}    

// This function returns elevation data from the Ordnance Survey OS Terrain 50 dataset.
// Data points are available as a 50 metre grid for the whole of The British Isles. 
// Returns: Elevation
// Input  : {Easting, Northing, ignored}
// ??? Consider adding a cache ???
// NS01
if (! function_exists('getElevation')) {
    function getElevation($xy=[]) {
        $east  = $xy[0];
        $north = $xy[1];
        $z     = 0;
        $rows  = [];

        $OSRef = new OSRef($east, $north);                      // Easting, Northing
        $OS2 = $OSRef->toTwoFigureReference();                  // Gives us the OS filename for our tile
     
        $path = $OS2.'.asc';
        $myrc = Storage::disk('OST50')->exists($path);
        if ($myrc) {                                            // Read & parse the file
            $data = Storage::disk('OST50')->get($path);
            $count = 1;
            foreach(preg_split("/((\r?\n)|(\r\n?))/", $data) as $line) {
                if ($count<=5) {                                // First 5 lines for controls        
                    $l1  = explode(' ', $line);
                    $l2  = $l1[0];
                    $$l2 = $l1[1];                              // Dynamic var assignment for $xllcorner, $yllcorner, $cellsize
                    ++$count;
                } else {                                        // Now we're into the data
                    $rows[] = $line;
                }
            }
            unset($data);                                       // Release storage

            if (isset($xllcorner, $yllcorner, $cellsize)) {
                $x = (int) ($east - $xllcorner) / $cellsize;            // NS01
                $y = (int) 199 - (($north - $yllcorner) / $cellsize);   // NS01
                if (isset($rows[$y])) {                                 // NS01
                    $row = explode(' ', $rows[$y]);                     // NS01
                    if (isset($row[$x])) {                              // NS01
                        $z = $row[$x];                                  // NS01
                    }    
                }
            }
        } 
        return $z;
    }
}    

// If you change Helpers.php you should do "composer dump-autoload".
// Returns a correction angle for CSS transform based on the File::->meta EXIF:Orientation   
if (! function_exists('getRotation')) {
    function getRotation(File $file) {
        $meta = json_decode($file->meta);
        if (isset($meta->Orientation)) {
            $angle = [0,0,0,180,0,0,90,0,270][$meta->Orientation];
        } else { $angle = 0; }
        return $angle; 
    }
}    

// If you change Helpers.php you should do "composer dump-autoload".
// Provides a thumnbnail of an image file
// Returns a binary string of the thumbnail or boolean
// NS01 NS02 NS03  
if (! function_exists('myThumb')) {
    function myThumb($path, $size='') {
        //dd($path, $size);
        $myrc = false;
        if (!is_numeric($size)) { $size = 100; }
        $tFile = 'thumb.jpg';
        $myrc = Image::make($path)->widen($size, function ($constraint) {
            $constraint->upsize();
        })->save($tFile);

        if ($myrc) { $myrc = $tFile; }
        return $myrc; 
    }
}    

// If you change Helpers.php you should do "composer dump-autoload".
// Rotates an image file
// Returns a binary string of the rotated image or boolean
// NS01 NS02 NS03  
if (! function_exists('myRotate')) {
    function myRotate(File $file, $angle=false) {
        $myrc = false;

        if (!is_numeric($angle)) {                          // NS01 NS03 Calculate based on EXIF:Orientation
            $meta = json_decode($file->meta);
            if (isset($meta->Orientation)) {
                $angle = [0,0,0,180,0,0,270,0,90][$meta->Orientation];
            }
        }

        if (!is_numeric($angle) or $angle<1 or $angle==360) { $angle = false; } // NS03    
        if ($angle) {
            if ($angle > 360) { $angle = rand(0,360); }     // NS02 Random angle
            $path = filePath($file);
            $source = imagecreatefromjpeg($path);
            //$white = imagecolorallocate($source, 255, 255, 255);
            $grey   = imagecolorallocate($source, 242, 242, 242);
            $rotate = imagerotate($source, $angle, $grey);
            ob_start();
            imagejpeg($rotate);
            $fileRotated = ob_get_contents();
            ob_end_flush();
            $myrc = $fileRotated;
        }
        return $myrc; 
    }
}    

// If you change Helpers.php you should do "composer dump-autoload".
// Wrapper for session array vars
// NS01
if (! function_exists('mySession')) {
    function mySession($tag, $key=false, $val=false) {
        $myrc = false;
        $tag = $tag == 'this' ? 'zone' : $tag;          // Zone
        $t = session($tag) ?: [];                       // NS01
        if ($key && $val) {                             // Write    
            $t[$key] = $val;
            session([$tag => $t]);      
            $myrc = true; 
        } elseif (array_key_exists($key, $t)) { 
            $myrc = $t[$key];                           // Read
        }
        return $myrc; 
    }
}    

// If you change Helpers.php you should do "composer dump-autoload".
// Supports dynamically adjustable paginator - per page size
if (! function_exists('pageSize')) {
    function pageSize($request, $tag, $default, $pageMin=false, $pageMax=false, $pageStep=false) {
        $pageMin  = $pageMin  ?:  5;
        $pageMax  = $pageMax  ?: 50;
        $pageStep = $pageStep ?:  5;

        $t = session($tag);
        if ($t) { $pageSize = array_key_exists('pageSize', $t) ? $t['pageSize'] : $default; }
        else { $pageSize = $default; } 
        $pageSize = $request->pp ? $request->pp : $pageSize;
        $pageSize = $pageSize < $pageMin ? $pageMin : $pageSize;
        $pageSize = $pageSize > $pageMax ? $pageMax : $pageSize;
        session([$tag => ['pageSize' => $pageSize]]);       // Save as a session value
        return ['size' => $pageSize, 'min' => $pageMin, 'max' => $pageMax, 'step' => $pageStep]; 
    }
}    

// If you change Helpers.php you should do "composer dump-autoload".
// Supports files.show First, Previous, Next, Last
// Supports files.showImage First, Previous, Stop, PlayList, Next, Last, Max, Current
if (! function_exists('showNav2')) {
    function showNav2($id, $list, $playList=false) {
        $list = $list ?: [];                  
        $max  = count($list);
        $item = array_search($id, $list);

        if ($item !== false) {
            if ($item > 0)      { $start = $list[0];       $prev = $list[$item-1]; }
            else                { $start = false;          $prev = false;          }
            if ($item < $max-1) { $next  = $list[$item+1]; $last = $list[$max-1];  } 
            else                { $next  = false;          $last = false;          }
            if ($playList) {
                $plsB = ['fas fa-genderless', 'fas fa-reply', 'fas fa-reply-all'][$playList-1];
                $plsV = $next ?: $start;
                $nav  = ['fas fa-fast-backward', $start, 'fas fa-step-backward', $prev,
                         'fas fa-stop',          true,   $plsB,                  $plsV,
                         'fas fa-step-forward',  $next,  'fas fa-fast-forward',  $last,
                         $max, $item+1];
            } else {
                $nav =  ['fas fa-fast-backward', $start, 'fas fa-step-backward', $prev,
                         'fas fa-step-forward',  $next,  'fas fa-fast-forward',  $last];
            }                                          
        } else { $nav = false; }
        //dd($id, $item, $nav, $list, $playList);
    return $nav;
    }
}    

// If you change Helpers.php you should do "composer dump-autoload".
// Supports files.show First, Previous, Next, Last, Max, Current
// NS01 NS02 NS03
if (! function_exists('showNav')) {
    function showNav($id, $list) {
        $list = $list ?: [];                                                        // NS01
        $max  = count($list);                                                       // NS02
        $item = array_search($id, $list);
        if ($item !== false) {
            if ($item > 0)      { $start = $list[0];       $prev = $list[$item-1]; }
            else                { $start = false;          $prev = false;          }
            if ($item < $max-1) { $next  = $list[$item+1]; $last = $list[$max-1];  } //NS02
            else                { $next  = false;          $last = false;          }    
            $nav = ['fas fa-fast-backward', $start, 'fas fa-step-backward', $prev,
                    'fas fa-step-forward',  $next,  'fas fa-fast-forward',  $last,
                    $max, $item+1, false];                                           // NS02 NS03
        } else { $nav = false; }
        //dd($id, $item, $nav, $list);
    return $nav;
    }
}    

// If you change Helpers.php you should do "composer dump-autoload".
// Extract embeded file tags
if (! function_exists('getMeta')) {
    function getMeta($file) {
        $fileName = $file->getClientOriginalName();         // fn.ft
        $pathExt = pathinfo($fileName, PATHINFO_EXTENSION); // ft
        $mime_type = $file->getMimeType();
        if (substr($mime_type, 0, 1) == 'a') {              // Audio ID3 tags
            $obj = new ID3Tags;
            $meta = $obj->getTags($file);
            if (!$meta) {                                   // Audio M4A tags
                $obj = new M4ATags;
                $meta = $obj->getTags($file);                
            }
        } elseif (substr($mime_type, 0 , 2) == 'im') {      // Image EXIF tags
            $obj = new EXIFTags;
            $meta = $obj->getTags($file);
        } elseif ($pathExt == 'gpx') {                      // Map GPX tags
            $obj = new GPXTags;
            $meta = $obj->getTags($file);
        } else { $meta = null; }
    return strlen($meta)<2 ? null : $meta;
    }
}    

// If you change Helpers.php you should do "composer dump-autoload".
// Status definitions for Folders
// $list['d'] for Folders
if (! function_exists('folderStatus')) {
    function folderStatus($default = -1) {
        $status = [
            '1' => 'Public',
            '0' => 'Private',
        ];
        if ($default >= 0) { $status[$default] = '*' . $status[$default]; }
        return $status;
    }
}    
// If you change Helpers.php you should do "composer dump-autoload".
// Status definitions for Posts
// $list['p'] for Posts
if (! function_exists('postStatus')) {
    function postStatus($default = -1) {
        $status = [
            '4' => 'Published',
            '3' => 'Under Review',
            '2' => 'In Draft',
            '1' => 'Withheld',
            '0' => 'Dead',
        ];
        if ($default >= 0) { $status[$default] = '*' . $status[$default]; }
        return $status;
    }
}
// If you change Helpers.php you should do "composer dump-autoload".
// Status definitions for Files
// $list['f'] for Files
if (! function_exists('fileStatus')) {
    function fileStatus($default = -1) {
        $status = postStatus($default);              // Same as Posts at the moment!
        //if ($default >= 0) { $status[$default] = '*' . $status[$default]; }
        return $status;
    }
}
// If you change Helpers.php you should do "composer dump-autoload".
// Status definitions for Albums
// $list['f'] for Albums
if (! function_exists('albumStatus')) {
    function albumStatus($default = -1) {
        $status = postStatus($default);              // Same as Posts at the moment!
        //if ($default >= 0) { $status[$default] = '*' . $status[$default]; }
        return $status;
    }
}
// If you change Helpers.php you should do "composer dump-autoload".
// Option definitions for Files
// $list['o'] for File Options
if (! function_exists('fileOption')) {
    function fileOption($default = -1) {
        $options = [
            '4' => 'Overwrite',
            '3' => 'Auto',
            '2' => 'Inject Name:',
            '1' => 'Skip',
            '0' => 'Fail',
        ];
        if ($default >= 0) { $options[$default] = '*' . $options[$default]; }
        return $options;
    }
}

// If you change Helpers.php you should do "composer dump-autoload".
// Returns the size of all files in a folder and its subfolders.
// Can optionally update the Folder object with the size
// NS01  
if (! function_exists('folderWithSize')) {
    function folderWithSize($folder, $update=false) {
        if (is_numeric($folder)) { $folder = Folder::find($folder); }               // NS01 Input is a Folder ID
        if (gettype($folder) == 'object') { $dir = folder_path($folder)->path; }    // Input is a Folder object
        else { $dir = $folder; $folder = false; }                                   // Input is a Directory

        $total_size = 0;
        if (file_exists($dir)) { 
            $count = 0;
            $dir_array = scandir($dir);
            foreach($dir_array as $key => $filename) {
                if($filename != ".." && $filename != ".") {
                    if(is_dir($dir . "/" . $filename)) {
                        $new_foldersize = foldersize($dir . "/" . $filename);
                        $total_size = $total_size + $new_foldersize;
                    } else if(is_file($dir . "/" . $filename)) {
                        $total_size = $total_size + filesize($dir . "/" . $filename);
                        $count++;
                    }
                }
            }
        }

        if ($folder && $update) {
            unset($folder->path); 
            $folder->size = $total_size;
            $folder->save();
        }    
        if ($folder) { return $folder; } else { return $total_size; }
    }
}

// If you change Helpers.php you should do "composer dump-autoload". 
if (! function_exists('folderSize')) {
    function folderSize($dir) {
	    $total_size = 0;
        if (file_exists($dir)) { 
    	    $count = 0;
    	    $dir_array = scandir($dir);
            foreach($dir_array as $key => $filename) {
            	if($filename != ".." && $filename != ".") {
               		if(is_dir($dir . "/" . $filename)) {
                  		$new_foldersize = foldersize($dir . "/" . $filename);
                  		$total_size = $total_size + $new_foldersize;
                	} else if(is_file($dir . "/" . $filename)) {
                  		$total_size = $total_size + filesize($dir . "/" . $filename);
                  		$count++;
                	}
           		}
     		}
        }    
		return $total_size;
	}
}

// If you change Helpers.php you should do "composer dump-autoload". 
if (! function_exists('msgx')) {
    function msgx($m=[]) {
    	if (!is_array($m)) { $m = ['Message' => [$m, true]]; }
        foreach ($m as $type => $rule) {
        	if (!array_key_exists(1, $rule)) { $rule[1] = true; }
        	if ($rule[1] == true) {
				$msgx = Session::get('msgx');
                $msgx[$type][] = $rule[0];
				session()->flash('msgx', $msgx);
        		break;
        	}
        } 
        return;
    }
}

// If you change Helpers.php you should do "composer composer dump-autoload". 
// Returns string
//  "C:\xampp\htdocs\tutorials\blog\private\folders\Superadministrator\test-a-private-folder"
if (! function_exists('private_path')) {
    function private_path($folder='') {
        $folder = !$folder ? '' : '\\' . $folder;
        return base_path().'\\private' . $folder;
    }
}
// Returns Folder:: with Folder->path
//  "C:\xampp\htdocs\tutorials\blog\private\folders\Superadministrator\test-a-private-folder"
if (! function_exists('folder_path')) {
    function folder_path(Folder $folder) {
        if ($folder->status == 1) {
            $folder->directory = 'folders\\' . $folder->slug;
            $folder->path = public_path($folder->directory);
        } else {
            $user = User::find($folder->user_id);
            $folder->directory = 'folders\\' . $user->name . '\\' . $folder->slug;
            $folder->path = private_path($folder->directory);
        } 
        return $folder;
    }
}

// Returns string
//  "C:\xampp\htdocs\tutorials\blog\public\folders\test-a-public-folder"
if (! function_exists('folderPath')) {
    function folderPath(Folder $folder) {
        if ($folder->status == 1) {                                                     // Public folder
            $folderDirectory = 'folders\\' . $folder->slug;
            $folderPath = public_path($folder->directory);
        } else {                                                                        // Private folder
            $user = User::find($folder->user_id);
            $folderDirectory = 'folders\\' . $user->name . '\\' . $folder->slug;
            $folderPath = private_path($folderDirectory);
        }            
        return $folderPath;
    }
}

// Returns string or boolean
//  "C:\xampp\htdocs\tutorials\blog\private\folders\Superadministrator\test-a-private-folder\filename.ft"
if (! function_exists('filePath')) {
    function filePath(File $file=null, $folder=false) {
        if (gettype($file) != 'object') { return false; }
        if (!$folder) { $folder = Folder::find($file->folder_id); }
        if (!$folder or gettype($folder) != 'object') { return false; }
        else { return folderPath($folder) . '\\' . $file->file; }
    }
}

// Returns string or boolean
//  "folders\test-a-public-folder\filename.ft"
if (! function_exists('fileURL')) {
    function fileURL(File $file, $folder=false) {
        if (!$folder) { $folder = Folder::find($file->folder_id); }
        if ($folder->status == 1) { return 'folders\\' . $folder->slug . '\\' . $file->file; }
        else                      { return false; }
    }    
}

// Returns string
//  nice size
if (! function_exists('mySize')) {
    function mySize($bytes, $unit=false) {
        if ($unit=='M') { $bytes = $bytes*1048576; }
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), [0,0,2,2,3][$i]).[' bytes',' kB',' M',' G',' T'][$i];
    }
}    

// Truncate and optionally strip HTML tags from long strings   
if (! function_exists('myTrim')) {
    function myTrim($string, $len=80, $term='~~', $strip=true) {
        $len1=intval($len*.75);
        $len2=($len-$len1)*-1;                  // -ive forces sunstr() to count back from end!!
        if ($strip) { $string=strip_tags($string); }
        return strlen($string)<=$len?$string:substr($string,0,$len1).$term.substr($string,$len2);
    }
}     

if (! function_exists('search_helper2')) {
    function search_helper2($search=false, $q) {
        $model = 'App\\' . $q['model'];
        $query = $model::select('*');

        if ($search) {
            if(gettype($search) == 'array') {
                $query->whereIn('id', $search);
            } else {
                // Build an array of search terms where phrases bounded by "" are treated as a single term
                // Also isolate the OR and AND search terms
                $search_listOr  = [];
                $search_listAnd = [];                  
                preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $search, $words);
                $and = false;
                foreach ($words[0] as $word) {
                    $word = trim($word, '"');
                    if (!strcasecmp($word, 'and')) { $and = true; continue; }
                    else if ($and)                 { $and = false; $search_listAnd[] = $word; }
                    else                           { $search_listOr[] = $word; }   
                }

                // Search each column in our model
                $search_list = $search_listOr;
                foreach ($q['searchModel'] as $column) {
                    foreach ($search_list as $word) {
                        $query->orWhere($column, 'LIKE', '%' . $word . '%');
                    }    
                }
                // Search each column in related models
                foreach ($q['searchRelated'] as $table => $columns) {
                    foreach ($columns as $column) {
                        foreach ($search_list as $word) {
                            $query->orWhereHas($table, function($qq) use ($column, $search, $word, $q){
                                $qq->where($column, 'LIKE', '%' . $word . '%');
                            }); 
                        }
                    }
                }

                // We are doing a complex search !!
                // Do it all again and use the intersect of the OR and AND queries 
                if (count($search_listAnd) > 0) {
                    $search_list = $search_listAnd;
                    $query2 = $model::select('*');
                    // Search each column in our model
                    foreach ($q['searchModel'] as $column) {
                        foreach ($search_list as $word) {
                            $query2->orWhere($column, 'LIKE', '%' . $word . '%');
                        }    
                    }
                    // Search each column in related models
                    foreach ($q['searchRelated'] as $table => $columns) {
                        foreach ($columns as $column) {
                            foreach ($search_list as $word) {
                                $query2->orWhereHas($table, function($qq) use ($column, $search, $word, $q){
                                    $qq->where($column, 'LIKE', '%' . $word . '%');
                                }); 
                            }
                        }
                    }

                    $q1 = $query ->get()->pluck('id')->toarray();
                    $q2 = $query2->get()->pluck('id')->toarray();
                    $query = $model::select('*')->whereIn('id', array_intersect($q1, $q2));
                } // End complex search

                // Eager load each related model
                foreach ($q['searchRelated'] as $table => $columns) {
                    $query = $query->with($table);
                }
            }    
        } 

        // Filter everything
        if (array_key_exists('filter', $q)) {
            $query->where(function ($qq) use ($q) {
                $qq->where($q['filter'][0], $q['filter'][1], $q['filter'][2]);
            });
        } 
        return $query;
    }
}

if (! function_exists('queryHelper')) {
    function queryHelper($q, $data=false) {
        // $data can be a Request containing ->search and/or ->sort data
        // $data can also be an array of ->id data         
        $model = 'App\\' . $q['model'];
        $query = $model::select('*');

        // Handle the array of ->id data ***************************************************************
        if (gettype($data) == 'array') {
            $query->whereIn('id', $data);
        }

        // Handle the ->search data ********************************************************************
        if (is_object($data)) {
            // Build an array of search terms where phrases bounded by "" are treated as a single term
            // Also isolate the OR and AND search terms
            $search = $data->search;
            $search_listOr  = [];
            $search_listAnd = [];                  
            preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $search, $words);
            $and = false;
            foreach ($words[0] as $word) {
                $word = trim($word, '"');
                if (!strcasecmp($word, 'and')) { $and = true; continue; }
                else if ($and)                 { $and = false; $search_listAnd[] = $word; }
                else                           { $search_listOr[] = $word; }   
            }

            // Search each column in our model
            $search_list = $search_listOr;
            foreach ($q['searchModel'] as $column) {
                foreach ($search_list as $word) {
                    $query->orWhere($column, 'LIKE', '%' . $word . '%');
                }    
            }
            // Search each column in related models
            foreach ($q['searchRelated'] as $table => $columns) {
                foreach ($columns as $column) {
                    foreach ($search_list as $word) {
                        $query->orWhereHas($table, function($qq) use ($column, $search, $word, $q){
                            $qq->where($column, 'LIKE', '%' . $word . '%');
                        }); 
                    }
                }
            }

            // We are doing a complex search !!
            // Do it all again and use the intersect of the OR and AND queries 
            if (count($search_listAnd) > 0) {
                $search_list = $search_listAnd;
                $query2 = $model::select('*');
                // Search each column in our model
                foreach ($q['searchModel'] as $column) {
                    foreach ($search_list as $word) {
                        $query2->orWhere($column, 'LIKE', '%' . $word . '%');
                    }    
                }
                // Search each column in related models
                foreach ($q['searchRelated'] as $table => $columns) {
                    foreach ($columns as $column) {
                        foreach ($search_list as $word) {
                            $query2->orWhereHas($table, function($qq) use ($column, $search, $word, $q){
                                $qq->where($column, 'LIKE', '%' . $word . '%');
                            }); 
                        }
                    }
                }

                $q1 = $query ->get()->pluck('id')->toarray();
                $q2 = $query2->get()->pluck('id')->toarray();
                $query = $model::select('*')->whereIn('id', array_intersect($q1, $q2));
            } // End complex search

            // Eager load each related model
            foreach ($q['searchRelated'] as $table => $columns) {
                $query = $query->with($table);
            }
        }    

        // Handle the ->sort data ************************************************************************
        if (array_key_exists('sortModel', $q)) {
            $sort = is_object($data) ? $data->sort : false;
            $sort = $sort ?: $q['sortModel']['default'];
            if (strlen($sort) == 3) {
                $sort = substr($sort, 0, 1) . (substr($sort, 2, 1) == 'a' ? 'd' : 'a');
            }
            $sortKey = substr($sort, 0, 1);
            if (array_key_exists($sortKey, $q['sortModel'])) {
                $s = explode(',', $q['sortModel'][$sortKey]);
                $sortDir = strlen($sort) > 1 ? substr($sort, 1, 1) : $s[0];
                $sortDir = $sortDir == 'a' ? 'asc' : 'desc';
                if (is_object($data)) {
                    $data->sort = $sortKey . substr($sortDir, 0, 1);
                }    
                if (count($s) == 2) {                                                      // Simple column sort
                    $query->orderBy($s[1], $sortDir);
                } else {                                                                   // Sort on Relationship
                    // Not yet available - can't work out how to do it yet!
                }
            } 
        }

        // Filter everything
        if (array_key_exists('filter', $q)) {
            $query->where(function ($qq) use ($q) {
                $qq->where($q['filter'][0], $q['filter'][1], $q['filter'][2]);
            });
        } 
        return $query;
    }
}
