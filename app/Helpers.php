<?php
use App\myLibs\ID3TagsReader  as ID3Tags;
use App\myLibs\EXIFTagsReader as EXIFTags;
use App\Album;
use App\Category;
use App\Comment;
use App\File;
use App\Folder;
use App\Permission;
use App\Photo;
use App\Post;
use App\Profile;
use App\Role;
use App\Tag;
use App\User;

// If you change Helpers.php you should do "composer dump-autoload".
// Wrapper for session array vars
if (! function_exists('mySession')) {
    function mySession($tag, $key=false, $val=false) {
        $myrc = false;
        $tag = $tag == 'this' ? 'zone' : $tag;          // Zone
        $t = session($tag);
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
if (! function_exists('showNav')) {
    function showNav($id, $list=[]) {
        $item = array_search($id, $list);
        if ($item !== false) {
            if ($item > 0)              { $start = $list[0];      $prev = $list[$item-1];        }
            else                        { $start = false;         $prev = false;                 }
            if ($item < count($list)-1) { $next = $list[$item+1]; $last = $list[count($list)-1]; }
            else                        { $next = false;          $last = false;                 }    
            $nav = ['fas fa-fast-backward', $start, 'fas fa-step-backward', $prev,
                    'fas fa-step-forward',  $next,  'fas fa-fast-forward',  $last];
        } else { $nav = false; }
        //dd($id, $item, $nav, $list);
    return $nav;
    }
}    

// If you change Helpers.php you should do "composer dump-autoload".
// Extract embeded file tags
if (! function_exists('getMeta')) {
    function getMeta($file) {
        $mime_type = $file->getMimeType();
        if (substr($mime_type, 0, 1) == 'a') {             // Audio ID3 tags
            $obj = new ID3Tags;
            $meta = $obj->getTags($file);
        } elseif (substr($mime_type, 0 , 2) == 'im') {     // Image EXIF tags
            $obj = new EXIFTags;
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
if (! function_exists('folderAndSize')) {
    function folderWithSize($folder, $update=false) {
        if (ctype_digit($folder)) { $folder = Folder::find($folder); }              // Input is a Folder ID
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
if (! function_exists('private_path')) {
    function private_path($folder='') {
        $folder = !$folder ? '' : '\\' . $folder;
        return base_path().'\\private' . $folder;
    }
}
if (! function_exists('folder_path')) {
    function folder_path($folder) {
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
if (! function_exists('folderPath')) {
    function folderPath($folder) {
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
if (! function_exists('filePath')) {
    function filePath($file=false, $folder=false) {
        if (gettype($file) != 'object') { return false; }
        if (!$folder) { $folder = Folder::find($file->folder_id); }
        if (!$folder or gettype($folder) != 'object') { return false; }
        else { return folderPath($folder) . '\\' . $file->file; }
    }
}
if (! function_exists('fileURL')) {
    function fileURL($file, $folder=false) {
        if (!$folder) { $folder = Folder::find($file->folder_id); }
        if ($folder->status == 1) { return 'folders\\' . $folder->slug . '\\' . $file->file; }
        else                      { return false; }
    }    
}

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


// If you change Helpers.php you should do "composer composer dump-autoload". 
// We don't hard code the model here so all the possible models that the
// controllers may request must be included up front
/*
	// Put the following near the top of your controller 
    public function searchQuery($search = '') {
        $query = [
            'model'         => 'Post',
            'searchModel'   => ['title', 'slug', 'image', 'body', 'excerpt'],
            'searchRelated' => [
				'user' 		=> ['name'],
				'category'  => ['name'],
				'tags' 		=> ['name'],
				'comments' 	=> ['email', 'name', 'comment' ]
            ],
            'filter'		=>['status', '>=', '4']
        ];
        return search_helper($search, $query);
    }

	// Put the following at the start of index function 
    $posts = $this->searchQuery($request->search)->orderBy('id', 'desc')->paginate(5);
*/ 
if (! function_exists('search_helper')) {
    function search_helper($search=false, $q) {
        $model = 'App\\' . $q['model'];
	    $query = $model::select('*');

        if ($search) {
   	    	// Build an array of search terms where phrases bounded by "" are treated as a single term
           	$search_list = [];
            preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $search, $words);
            foreach ($words[0] as $word) {
                $search_list[] = trim($word, '"');
            }  

	    	// Eager load each related model
	        foreach ($q['searchRelated'] as $table => $columns) {
	        	$query=$query->with($table);
	        }

	    	// Search each column in our model
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







if (! function_exists('search_helperOld')) {
    function search_helperOld($search=false, $q) {
        $model = 'App\\' . $q['model'];
        $query = $model::select('*');

        if ($search) {
            // Build an array of search terms where phrases bounded by "" are treated as a single term
            $search_list = [];
            preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $search, $words);
            foreach ($words[0] as $word) {
                $search_list[] = trim($word, '"');
            }  

            // Eager load each related model
            foreach ($q['searchRelated'] as $table => $columns) {
                $query=$query->with($table);
            }

            // Search each column in our model
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
