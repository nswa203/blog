<?php

namespace App\Http\Controllers;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File as FileSys;
use App\File;
use App\Folder;
use App\Photo;
use App\Tag;
use Response;
use Session;
use Storage;
use URL;
use Validator;
use App\myLibs\TestClass as Test;

class FileController extends Controller
{

    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            session(['zone' => 'Files']);
            return $next($request);
        });
    }

    // This Query Builder searches our table/columns and related_tables/columns for each word/phrase.
    // The table is sorted (ascending or descending) and finally filtered.
    // It requires the custom queryHelper() function in Helpers.php.
    public function query() {
        $query = [
            'model'         => 'File',
            'searchModel'   => ['title', 'file', 'mime_type', 'meta'],
            'searchRelated' => [
                'folder'  => ['name', 'slug', 'description'],
                'tags'    => ['name'],
            ],    
            'sort'        => [
                'i'       => 'd,id',                                                      
                't'       => 'a,title',                                           
                's'       => 'd,size',                                            
                'p'       => 'd,status',
                'f'       => 'a,name,folder',
                'default' => 'i'                       
            ]
        ];
        return $query;
    }

    /* **************************************************************************************************************** 
    *  Index                                                                                                          *
    **************************************************************************************************************** */
    public function index(Request $request) {
        $files = paginateHelper($this->query(), $request, 12, 4, 192, 4); // size($request->pp), default, min, max, step
// Test code remove ****************************************************************************************

// Test code remove ****************************************************************************************
        $list['f'] = fileStatus();
        $list['d'] = folderStatus();

        if ($files && $files->count() > 0) {
            $x = queryHelperTest($this->query(), $request)->pluck('id')->toArray();
            mySession('filesIndex', 'index', $x);
            mySession('filesShow', 'indexURL', $request->url().'?'.$request->getQueryString());
        } else {
            Session::flash('failure', 'No Files were found.');
        }
        return view('manage.files.index', ['files' => $files, 'search' => $request->search, 'sort' => $request->sort, 'list' => $list]);
     }
    
    /* **************************************************************************************************************** 
    *  IndexOf                                                                                                        *
    **************************************************************************************************************** */     
    public function indexOf(Request $request, $folder_id) {
        $files = paginateHelper($this->query(), $request, 12, 4, 192, 4); // size($request->pp), default, min, max, step

        $list['f'] = fileStatus();
        $list['d'] = folderStatus();   
        
        if ($files && $files->count() > 0) {
            $x = queryHelperTest($this->query(), $request)->where('folder_id', $folder_id)->pluck('id')->toArray();
            mySession('filesIndex', 'index', $x);
            mySession('filesShow', 'indexURL', $request->url().'?'.$request->getQueryString());
        } else {
            Session::flash('failure', 'No Files were found.');
        }
        return view('manage.files.index', ['files' => $files, 'search' => $folder_id, 'sort' => $request->sort, 'list' => $list]);
     }      

    /* **************************************************************************************************************** 
    *  Create                                                                                                         *
    **************************************************************************************************************** */ 
    public function create() {
        $folders = Folder::orderBy('slug', 'asc')->pluck('slug', 'id');
        $tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');
        $file = new File;
        $list['f'] = fileStatus(2);
        $list['o'] = fileOption(3);
        $mimes = 'text/*,image/*,audio/*,video/*,.pdf,.txt,.log,.ico,.nfo,.nft,.srt,.rex,.rexx,.bat,.cmd,.php,.js,.rar,.zip,.gpx';

        return view('manage.files.create', ['file' => $file, 'folders' => $folders, 'tags' => $tags,
            'mimes' => $mimes, 'list' => $list, 'folder_id' => null]);
    }

    /* **************************************************************************************************************** 
    *  CreatIn                                                                                                        *
    **************************************************************************************************************** */ 
    public function createIn($folder_id) {
        $folder = Folder::findOrFail($folder_id);
        $folders = [$folder->id => $folder->slug];
        $tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');
        $file = new File;
        $list['f'] = fileStatus(2);
        $list['o'] = fileOption(3);
        $mimes = 'text/*,image/*,audio/*,video/*,.pdf,.txt,.log,.ico,.nfo,.nft,.srt,.rex,.rexx,.bat,.cmd,.php,.js,.rar,.zip,.gpx';

        return view('manage.files.create', ['file' => $file, 'folders' => $folders, 'tags' => $tags,
            'mimes' => $mimes, 'list' => $list, 'folder_id' => $folder_id]);
    }

    /* **************************************************************************************************************** 
    *  Store                                                                                                          *
    **************************************************************************************************************** */ 
    public function store(Request $request) {
        $files = Input::file('files');              // Provide Mime info msgs just in case validate triggers a failure  
        for ($i=0; $i<count($files); ++$i) {
            $msg = 'MimeType File.' . $i . ': ' . $files[$i]->getMimeType();
            msgx(['info' => [$msg, true]]);
        }

        // We use our own validator here as auto validation cannot handle error return from a POST method 
        $validator = Validator::make($request->all(), [
            'title'     => 'sometimes|max:191',
            'files'     => 'required|array|between:1,64',
            'files.*'   => 'filled|max:10000000|mimetypes:audio/*,video/*,image/*,application/pdf,text/plain,application/octet-stream,application/zip,text/html,application/x-7z-compressed,text/x-php,text/x-msdos-batch,text/xml',
            'status'    => 'required|integer|min:0|max:4',
            'option'    => 'required|integer|min:0|max:4',
            'folder_id' => 'required|integer|exists:folders,id',
            'tags'      => 'array',
            'tags.*'    => 'integer|exists:tags,id',
        ]);
        if ($validator->fails()) {
            return redirect()->route('files.create')->withErrors($validator)->withInput();
        }

        Session::put('msgx', []);                   // If we got here validate worked so remove info msgs 

        $folder   = folderWithSize($request->folder_id);
        $countBad = count($files);
        $count    = 0;
        $request->action = 'store';
        $request->folderTarget = $folder;

        foreach ($files as $item) {
            $request->fileSource = $count;
            $file = new File;
            ++$count;
            $file->title        = $request->title ? $request->title :
                ($request->option == 2 ? '%basename% %date% %time%.%baseext%' :'%basename%.%baseext%');
            $file->status       = $request->status;
            $file->folder_id    = $request->folder_id;
            $file->size         = $item->getSize();
            $file->published_at = $file->status == '4' ? date('Y-m-d H:i:s') : null;
            $fileName = $item->getClientOriginalName();                                     // fn.ft
            $fileWrap = myTrim($fileName, 48);                                              // fn... ft
            $filePath = folder_path($folder)->path . '\\' . $fileName;                      // C:\folder\fn.ft
            $pathName = pathinfo($fileName, PATHINFO_FILENAME);                             // fn
            $pathExt  = pathinfo($fileName, PATHINFO_EXTENSION);                            // ft
            $file->file = $fileName;                                                        // fn.ft
            // Replacements ------------------------------------------------------------------------------------------
            $needles = ['%title%', '%filename%', '%basename%', '%baseext%', '%size%', '%folder%', '%date%', '%time%'];
            $replace = [$file->title, $fileName, $pathName, $pathExt, $file->size, $folder->name, date('Y-m-d'), date('H-m-s') ];
            $file->title = str_replace($needles, $replace, $file->title);
           
             // Check for existing file with same name ----------------------------------------------------------------
            $myrc = FileSys::exists($filePath); 
            if ($myrc) {
                $msg = 'File '.$count.': "'.$fileWrap.'" already exists in Folder "'.$folder->name.'".';
                if     ($request->option == 0) { msgx(['warning' => [$msg, true]]); break; }
                elseif ($request->option == 1) { msgx(['info'    => [$msg, true]]); continue; }
                elseif ($request->option == 2) { $fileNameAlt = $file->title.'.'.$pathExt; }
                elseif ($request->option == 3) {
                    for ($i=1; $i<=100; ++$i) {
                        $fileNameAlt = $pathName.'_'.$i.'.'.$pathExt;
                        $p = folder_path($folder)->path.'\\'.$fileNameAlt;
                        if (!FileSys::exists($p)) { break; }    
                    }
                }
                else { 
                    $msg = '';
                    $fileNameAlt = false;
                }

                if ($fileNameAlt) {
                    $file->file = $fileNameAlt;
                    $fileWrapAlt = myTrim($fileNameAlt, 48);
                    $msg = 'File '.$count.': "'.$fileWrapAlt.'" already exists in Folder "'.$folder->name.'".';
                    $filePath = folder_path($folder)->path.'\\'.$fileNameAlt;
                    if (FileSys::exists($filePath)) { msgx(['warning' => [$msg, true]]); break; }    
                    $msg = 'File '.$count.': "'.$fileWrap.'" auto renamed to "'.$fileWrapAlt.'".';
                    msgx(['info' => [$msg, true]]);                        
                }
            }                
            $request->pathTarget = $filePath;
            $myrc = $file->save();
            if ($myrc) {
                --$countBad;
                $msg = 'File '.$count.': saved as "'.myTrim($file->file, 48).'".';
                msgx(['info' => [$msg, true]]);                        
            } else {
                $msg = 'File '.$count.': "'.myTrim($file->file, 48).'" could not be saved to "'.$folder->slug.'"!';
                msgx(['warning' => [$msg, true]]);
            }
        } // EndForEach

        if ($myrc && $countBad == 0) {
            Session::flash('success', 'All Files were successfully saved.');
            if ($request->ajax()) {
                return json_encode(['countBad' => $countBad, 'url' => route('files.indexOf', $folder->id)]);    
            }            
            return redirect()->route('files.indexOf', $folder->id);
        } else {
            $count = count($files);
            Session::flash('failure',
                $countBad == $count ? 'No Files were saved.' : $countBad.' of '.$count.' Files were NOT saved.');
            if ($request->ajax()) {
                return json_encode(['countBad' => $countBad]);    
            }              
            return redirect()->route('files.create')->withInput();
        }
    }
    
    /* **************************************************************************************************************** 
    *  Show                                                                                                           *
    **************************************************************************************************************** */ 
    public function show($id) {
        $file = File::where('id', $id)->with('folder')->first();
        
        if ($file) {
            $file->ext = pathinfo($file->file, PATHINFO_EXTENSION);
            $list['f'] = fileStatus();
            $list['d'] = folderStatus();
            $list['x'] = showNav3($id, mySession('filesIndex', 'index'));     // Build Playlist navigation controls 

            return view('manage.files.show', ['file' => $file, 'meta' => json_decode($file->meta), 'list' => $list, 'search' => true]);
        } else {
            Session::flash('failure', 'File "' . $id . '" was NOT found.');
            return redirect()->route('files.index');
        }
    }

    /* **************************************************************************************************************** 
    *  ShowFile                                                                                                       *
    **************************************************************************************************************** */ 
    public function showFile(Request $request, $id)
    {
        $file = File::where('id', $id)->with('folder')->first();
        $playList = $request->pl ?: 1;

        if ($file) {
            $file->ext = pathinfo($file->file, PATHINFO_EXTENSION);
            $list['x'] = showNav3($id, mySession('filesIndex', 'index'));

            return view('manage.files.showImage', ['file' => $file, 'meta' => json_decode($file->meta), 'list' => $list, 'search' => true]);
        } else {
            Session::flash('failure', 'File "' . $id . '" was NOT found.');
            return redirect()->route('files.index');
        }
    }

    /* **************************************************************************************************************** 
    *  GetFile                                                                                                        *
    *  Hint: debug with http://blog/manage/private/nnn?r=angle       (where nnn = file number)                        *
    *  NS01 NS02 NS03 NS04                                                                                            *
    *  Debug: if "&" present, ensure you use !!bangs!! "{!! route('private.getFile', [':id', 'r=n&t=400']) !!}"       *    
    **************************************************************************************************************** */ 
    public function getFile(Request $request, $id) {
        $myrc = false;
        $file = File::find($id);
        if ($file) {
            $path = filePath($file);
            if (File::exists($path)) {
                $type = FileSys::mimeType($path);
                if ($request->t == 'y' or is_numeric($request->t)) {                // NS02
                    //$path = myThumb($path, $request->t);
                    //$file = FileSys::get($path);
                    $file = myThumb2($path, $request->t);                           // NS03
                }            
                else if ($request->r == 'y' or is_numeric($request->r)) {           // NS01 NS02
                    $file = myRotate($file, $request->r);
                } else {
                    $file = false;                                                  // NS04
                }
                if (! $file) { $file = FileSys::get($path); }
                $response = Response::make($file, 200);
                $response->header("x-file", $path);
                $response->header("x-size", strlen($file));                         // NS05
                $myrc = true;
            }
        }    
        if ($myrc) { return $response; }
        else { abort(404); }
    }

    /* **************************************************************************************************************** 
    *  FindFile                                                                                                       *
    **************************************************************************************************************** */ 
    public function findFile($fileTitle, $folderSlug) {
        $myrc   = false;
        $folder = Folder::where('slug', $folderSlug)->first();
        if ($folder) {
            $file = File::where('title', $fileTitle)->where('folder_id', $folder->id)->first();
            if ($file) {
                $path = filePath($file, $folder);
                if (File::exists($path)) {
                    $file = FileSys::get($path);
                    $type = FileSys::mimeType($path);
                    $response = Response::make($file, 200);
                    $response->header("Content-Type", $type);
                    $myrc = true;
                }    
            } 
        }

        if ($myrc) { return $response; }
        else { return 'FileController@findFile: "'.$fileTitle.'" not found in folder "'.$folderSlug.'".'; abort(404); } 
    }

    /* **************************************************************************************************************** 
    *  Edit                                                                                                           *
    **************************************************************************************************************** */ 
    public function edit($id) {
        $file = File::find($id);
        
        if ($file) {
            $folders = Folder::orderBy('slug', 'asc')->pluck('slug', 'id');
            $tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');
            $list['d'] = folderStatus();
            $list['f'] = fileStatus();
            $list['o'] = fileOption(3);
            $mimes = 'text/*,image/*,audio/*,video/*,.pdf,.txt,.log,.ico,.nfo,.nft,.srt,.rex,.rexx,.bat,.cmd,.php,.js,.rar,.zip,.gpx';

            $file->ext = pathinfo($file->file, PATHINFO_EXTENSION);                            // ft

            return view('manage.files.edit', ['file' => $file, 'folders' => $folders, 'tags' => $tags,
                'mimes' => $mimes, 'list' => $list, 'folder_id' => null, 'meta' => json_decode($file->meta)]);
        } else {
            Session::flash('failure', 'File "' . $id . '" was NOT found.');
            return redirect()->route('files.index');
        }
    }

    /* **************************************************************************************************************** 
    *  Update                                                                                                         *
    **************************************************************************************************************** */ 
    public function update(Request $request, $id) {
        $file = File::findOrFail($id);

        // We use our own validator here as auto validation cannot handle error return from a POST method 
        $validator = Validator::make($request->all(), [
            'title'         => 'sometimes|max:191',
            'files'         => 'sometimes|array|between:1,64',
            'files.*'       => 'filled|max:10000000|mimetypes:audio/*,video/*,image/*,application/pdf,text/plain,application/octet-stream,application/zip,text/html,application/x-7z-compressed,text/x-php,text/x-msdos-batch,text/xml',
            'status'        => 'required|integer|min:0|max:4',
            'folder_id'     => 'required|integer|exists:folders,id',
            'tags'          => 'array',
            'tags.*'        => 'integer|exists:tags,id',
        ]);
        if ($validator->fails()) {
            return redirect()->route('files.edit', $file->id)->withErrors($validator)->withInput();
        }

        if ($request->delete_image) {
            $msg = 'Delete File is not implemented in Edit - hit the Red Delete button!';
            msgx(['warning' => [$msg, true]]);
            return redirect()->route('files.show', $file->id);
        }            

        $folder = folderWithSize($file->folder_id);

        // Replace 
        $item = Input::file('files')[0];
        if ($item) {
            $request->action = 'replace';
            $request->fileSource = 0;
            $folder->size = $folder->size - $file->size;
            $request->folderTarget = $folder;
            $request->originalFilePath = folder_path($folder)->path . '\\' . $file->file;    // C:\folder\fn.ft
            $file->size = $item->getSize();
            $file->file = $item->getClientOriginalName();                                    // fn.ft
            $myrc = $file->save();
            $folder_id = $folder->id;
        }

        // Move
        if ($request->folder_id != $file->folder_id) {
            $request->action = 'move';
            $request->fileSource = filePath($file, $folder);
            $folderTarget = Folder::find($request->folder_id);
            $request->folderTarget = $folderTarget;
            $file->folder_id = $folderTarget->id;
            $myrc = $file->save();
            $folder_id = $folderTarget->id;
        } 

        // Alter
        $request->action = 'alter';
        $file->title = $request->title;
        $file->status = $request->status;
        if ($file->status == 4){
            if ($file->published_at == null) { $file->published_at = date('Y-m-d H:i:s'); }
        } else                               { $file->published_at = null; }
        $file->status = $request->status;
        $fileName = $file->file;
        $fileWrap = myTrim($fileName, 48);                                              // fn... ft
        $pathName = pathinfo($fileName, PATHINFO_FILENAME);                             // fn
        $pathExt  = pathinfo($fileName, PATHINFO_EXTENSION);                            // ft
        $filePath = folder_path($folder)->path . '\\' . $fileName;                      // C:\folder\fn.ft

        // Replacements ------------------------------------------------------------------------------------------
        $needles = ['%title%', '%filename%', '%basename%', '%baseext%', '%size%', '%folder%', '%date%', '%time%'];
        $replace = [$file->title, $fileName, $pathName, $pathExt, $file->size, $folder->name, date('Y-m-d'), date('H-m-s') ];
        $file->title = str_replace($needles, $replace, $file->title);

        $myrc = $file->save();
        $folder_id = $file->folder_id;

        if ($myrc) {
            Session::flash('success', 'File "' . $fileWrap .'" saved successfully.');
            if ($request->ajax()) {
                return json_encode(['countBad' => '0', 'url' => route('files.show', $file->id)]);    
            }
            return redirect()->route('files.show', $file->id);
        } else {
            Session::flash('failure', 'File was NOT saved.');
            if ($request->ajax()) {
                return json_encode(['countBad' => '1']);    
            }            
            return redirect()->route('files.edit', $file->id)->withInput();
        }
    }    

    /* **************************************************************************************************************** 
    *  Many                                                                                                           *
    *   manyCopy                                                                                                      *
    *   manyDelete                                                                                                    *
    *   manyEdit                                                                                                      *
    *   manyMove                                                                                                      *
    *   manyShow                                                                                                      *
    *   manyShowFile                                                                                                  *
    **************************************************************************************************************** */ 
    public function many(Request $request) {
//  dd($request);
        $choice = explode(',', $request->choice);
        $ids = $request->itemsSelected;
        if ($ids) { $ids = explode(',', $ids); }
        else      { $ids = [$choice[1]]; }

    /* **************************************************************************************************************** 
    *   manyCopy                                                                                                      *
    **************************************************************************************************************** */ 
        if ($choice[0] == 'copy') {
          //$files = $this->searchSortQuery($ids)->get();
            $files = queryHelperTest($this->query(), $ids)->get();

            $folders = Folder::orderBy('slug', 'asc')->pluck('slug', 'id');

            $list['f'] = fileStatus();
            $list['d'] = folderStatus();            
            return view('manage.files.manyCopy', ['files' => $files, 'folders' => $folders, 'search' => true, 'sort' => $request->sort, 'list' => $list, 'itemsSelected' => implode(',', $ids)]);
        }    
    /* **************************************************************************************************************** 
    *   manyDelete                                                                                                    *
    **************************************************************************************************************** */
        else if ($choice[0] == 'delete') {
       //$files = $this->searchSortQuery($ids)->get();
            $files = queryHelperTest($this->query(), $ids)->get();

            $list['f'] = fileStatus();
            $list['d'] = folderStatus();
            return view('manage.files.manyDelete', ['files' => $files, 'search' => true, 'sort' => $request->sort, 'list' => $list, 'itemsSelected' => implode(',', $ids)]);
        }    
    /* **************************************************************************************************************** 
    *   EditMultiple                                                                                                  *
    **************************************************************************************************************** */
        else if ($choice[0] == 'edit') {
        //$files = $this->searchSortQuery($ids)->get();
            $files = queryHelperTest($this->query(), $ids)->get();

            $tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');
            
            $list['f'] = fileStatus();
            $list['d'] = folderStatus();            
            return view('manage.files.manyEdit', ['files' => $files, 'tags' => $tags, 'search' => true, 'sort' => $request->sort, 'list' => $list, 'itemsSelected' => implode(',', $ids)]);        }
    /* **************************************************************************************************************** 
    *   manyMove                                                                                                      *
    **************************************************************************************************************** */    
        else if ($choice[0] == 'move') {
      //$files = $this->searchSortQuery($ids)->get();
            $files = queryHelperTest($this->query(), $ids)->get();

            $folders = Folder::orderBy('slug', 'asc')->pluck('slug', 'id');

            $list['f'] = fileStatus();
            $list['d'] = folderStatus();            
            return view('manage.files.manyMove', ['files' => $files, 'folders' => $folders, 'search' => true, 'sort' => $request->sort, 'list' => $list, 'itemsSelected' => implode(',', $ids)]);
        }      
    /* **************************************************************************************************************** 
    *   manyShowFile                                                                                                  *
    **************************************************************************************************************** */    
        else if ($choice[0] == 'showFile') {
            $ids = $request->itemsSelected;
            if ($ids) {
                $ids = explode(',', $ids);
                mySession('filesIndex', 'index', $ids);
            } else { $ids = [$choice[1]]; }
            return redirect()->route('files.manyShowFile', [$ids[0], 'r=y']); 
        }      
    /* **************************************************************************************************************** 
    *   manyShow                                                                                                      *
    **************************************************************************************************************** */   
        else {
            $ids = $request->itemsSelected;
            if ($ids) {
                $ids = explode(',', $ids);
                mySession('filesIndex', 'index', $ids);
            } else { $ids = [$choice[1]]; }
            return redirect()->route('files.show', $ids[0]);    
        }
    /* **************************************************************************************************************** 
    *   Error                                                                                                         *
    **************************************************************************************************************** */ 
        dd('Many Error!');
    }

    /* **************************************************************************************************************** 
    *  manyUpdate                                                                                                     *
    **************************************************************************************************************** */ 
    public function manyUpdate(Request $request) {
        $validator = Validator::make($request->all(), [
            'status*'   => 'sometimes|integer|min:0|max:4',
            'tags'      => 'array',
            'tags.*'    => 'integer|exists:tags,id',
        ]);
        if ($validator->fails()) {
            return redirect()->route('files.index')->withErrors($validator)->withInput();
        }

        $myrc = true;
        $request->action = 'alter';
        $files = explode(',', $request->itemsSelected);
        $countBad = count($files);
        $count    = 0;

        foreach ($files as $id) {
            ++$count;
            $file = File::with('tags')->find($id);
            $file->status = $request->status[0] ? $request->status[0] : $file->status;
            $file->published_at = $file->status == '4' ? date('Y-m-d H:i:s') : null;

            if ($file) {
                $myrc = $file->save();
                if ($myrc) {
                    $msg = 'File '.$count.': "'.$file->title.'" saved.';
                    msgx(['info' => [$msg, true]]);
                    $msg = 'Disk file '.$count.': "'.$file->file.'" saved.';
                    msgx(['info' => [$msg, true]]);
                    --$countBad;
                } else {
                    $msg = 'File '.$count.': "'.$file->title.'" could not be saved!';
                    msgx(['warning' => [$msg, true]]);
                }
            }    
        }

        if ($myrc && $countBad == 0) {
            Session::flash('success', 'All Files were successfully saved.');
            return redirect()->route('files.index');
        } else {
            $count = count($files);
            Session::flash('failure',
                $countBad == $count ? 'No Files were saved.' : $countBad.' of '.$count.' Files were NOT saved.');
            return redirect()->route('files.index')->withInput();
        }
    }    

    /* **************************************************************************************************************** 
    *  manyCopy                                                                                                       *
    *  FileObserver handles physical copy of the disk file and ensures no collisions                                  *  
    **************************************************************************************************************** */ 
    public function manyCopy(Request $request) {
        $validator = Validator::make($request->all(), [
            'folder_id' => 'required|integer|exists:folders,id'
        ]);
        if ($validator->fails()) {
            return redirect()->route('files.index')->withErrors($validator)->withInput();
        }

        $myrc = true;
        $request->action = 'copy';
        $files = explode(',', $request->itemsSelected);
        $countBad = count($files);
        $count    = 0;

        $folderTarget = Folder::findOrFail($request->folder_id);
        $request->folderTarget = $folderTarget;

        foreach ($files as $id) {
            ++$count;
            $fileSource = File::with('tags')->find($id);
            if ($fileSource) {
                $folderSource = Folder::find($fileSource->folder_id);
                $fileTarget = $fileSource->replicate();
                $fileTarget->folder_id = $folderTarget->id;
                $request->fileSource = filePath($fileSource, $folderSource);
                $request->tags = $fileSource->tags->pluck('id');

                $myrc = $fileTarget->save();
                if ($myrc) {
                    $msg = 'File '.$count.': "'.$fileSource->title.'" copied to "'.$folderTarget->slug.'".';
                    msgx(['info' => [$msg, true]]);
                    $msg = 'Disk file '.$count.': "'.$fileSource->file.'" copied to"'.$folderTarget->directory.'".';
                    msgx(['info' => [$msg, true]]);
                    --$countBad;
                } else {
                    $msg = 'File '.$count.': "'.$fileSource->title.'" could not be copied to "'.$folderTarget->slug.'"!';
                    msgx(['warning' => [$msg, true]]);
                }
            }    
        }

        if ($myrc && $countBad == 0) {
            Session::flash('success', 'All Files were successfully copied.');
            return redirect()->route('files.index');
        } else {
            $count = count($files);
            Session::flash('failure',
                $countBad == $count ? 'No Files were copied.' : $countBad.' of '.$count.' Files were NOT copied.');
            return redirect()->route('files.index')->withInput();
        }
    }    

    /* **************************************************************************************************************** 
    *  manyMove                                                                                                       *
    *  FileObserver handles physical move of the disk file and ensures no collisions                                  *  
    **************************************************************************************************************** */ 
    public function manyMove(Request $request) {
        $validator = Validator::make($request->all(), [
            'folder_id' => 'required|integer|exists:folders,id'
        ]);
        if ($validator->fails()) {
            return redirect()->route('files.index')->withErrors($validator)->withInput();
        }

        $myrc = true;
        $request->action = 'move';
        $files = explode(',', $request->itemsSelected);
        $countBad = count($files);
        $count    = 0;

        $folderTarget = Folder::findOrFail($request->folder_id);
        $request->folderTarget = $folderTarget;

        foreach ($files as $id) {
            ++$count;
            $file = File::find($id);
            if ($file) {
                $folderSource = Folder::find($file->folder_id);
                $request->fileSource = filePath($file, $folderSource);
                $file->folder_id = $folderTarget->id;

                $myrc = $file->save();
                if ($myrc) {
                    $msg = 'File '.$count.': "'.$file->title.'" moved to "'.$folderTarget->slug.'".';
                    msgx(['info' => [$msg, true]]);
                    $msg = 'Disk file '.$count.': "'.$file->file.'" moved to"'.$folderTarget->directory.'".';
                    msgx(['info' => [$msg, true]]);
                    --$countBad;
                } else {
                    $msg = 'File '.$count.': "'.$file->title.'" could not be moved to "'.$folderTarget->slug.'"!';
                    msgx(['warning' => [$msg, true]]);
                }
            }    
        }

        if ($myrc && $countBad == 0) {
            Session::flash('success', 'All Files were successfully moved.');
            return redirect()->route('files.index');
        } else {
            $count = count($files);
            Session::flash('failure',
                $countBad == $count ? 'No Files were moved.' : $countBad.' of '.$count.' Files were NOT moved.');
            return redirect()->route('files.index')->withInput();
        }
    }    

    /* **************************************************************************************************************** 
    *  manyDestroy                                                                                                    *
    *  FileObserver handles physical removal of the disk file and general clean up.                                   *  
    **************************************************************************************************************** */ 
    public function manyDestroy(Request $request) {
        $myrc = true;
        $files = explode(',', $request->itemsSelected);
        $countBad = count($files);
        $count    = 0;
        
        foreach ($files as $id) {
            ++$count;
            $file = File::findOrFail($id);
            if ($file) {
                $myrc = $file->delete();
                if ($myrc) {
                    $msg = 'File '.$count.': "'.$file->title.'" deleted.';
                    msgx(['info' => [$msg, true]]);
                    $msg = 'Disk file '.$count.': "'.$file->file.'" erased.';
                    msgx(['info' => [$msg, true]]);
                    --$countBad;
                }
            }    
        }

        if ($myrc && $countBad == 0) {
            Session::flash('success', 'All Files were successfully deleted.');
            return redirect()->route('files.index');
        } else {
            $count = count($files);
            Session::flash('failure',
                $countBad == $count ? 'No Files were deleted.' : $countBad.' of '.$count.' Files were NOT deleted.');
            return redirect()->route('files.delete')->withInput();
        }
    }

    /**
     * API Get Elevation Data using Ordnance Survey OS Terrain 50 data 
     *     Calculate Distances for multiple points.
     *     Added Calories burned data.
     * We check if any Way Points are near our route and if so, we add        
     */
    public function apiGetElevation(Request $request) {
        $file = File::findOrFail($request->id);
        $data = json_decode($file->meta);
        $tPoints = isset($data->rtept) ? $data->rtept : $data->trkpt;       // Track or Route points
        $wPoints = isset($data->wpt)   ? $data->wpt   : [];                 // Way or Marker points
// dd($data->wpt);

        //$distance = isset($data->Distance) ? $data->Distance : 0;
        //$climb    = isset($data->Climb   ) ? $data->Climb    : 0;
        $minY = isset($data->Lowest ) ? $data->Lowest  : 0;
        $maxY = isset($data->Highest) ? $data->Highest : 0;

// Add range max min etc for calories
// do calcs for calories        
        $data1 = [];                            // Elevation 
        $data2 = [];                            // Climb
        $data3 = [];                            // Waypoints            
        $data4 = [];                            // Calories person profile 1
        $data5 = [];                            // Calories person profile 2
        $data6 = [];                            // Calories person profile 3
        $c  = 0.0;                              // Elevation
        $d  = 0.0;                              // Distance
        $e1 = 0.0;                              // Calories 1
        $e2 = 0.0;                              // Calories 2
        $e3 = 0.0;                              // Calories 3
        $m  = 1609.344;                         // Metres in a mile (Change to 1 for x-axis in metres)

        for ($i=0; $i<sizeof($tPoints); ++$i) {
            $tPoint = $tPoints[$i];
            $x = isset($tPoint->d  ) ? $tPoint->d   : 0;                // delta Distance
            $y = isset($tPoint->ele) ? $tPoint->ele : 0;                // Elevation
            $d = $d + $x;                                               // Total Distance
            if($i>0 && $y>$eleOld) { $c = $c + $y - $eleOld; }          // Total Climb
            if($i>0) {                                                  // Total Calories
                $e1 = $e1 + calories($x, $y - $eleOld)[0];              // Nick
                $e2 = $e2 + calories($x, $y - $eleOld)[1];              // Dave
                $e3 = $e3 + calories($x, $y - $eleOld)[2];              // Chris
            }     
            $data1[] = ['x' => round($d/$m, 3), 'y' => round($y,  1)];
            $data2[] = ['x' => round($d/$m, 3), 'y' => round($c,  1)];
            $data4[] = ['x' => round($d/$m, 3), 'y' => round($e1, 1)];
            $data5[] = ['x' => round($d/$m, 3), 'y' => round($e2, 1)];
            $data6[] = ['x' => round($d/$m, 3), 'y' => round($e3, 1)];
            $eleOld = $tPoint->ele;

            // Ignore Start & Finish waypoints
            // We use a rounding of 3 dec places for accuracy but we may miss a nearby waypoint 
            for ($j=0; $j<sizeof($wPoints); ++$j) {                     // Here we check if our Waypoints are nearby to our track 
                if (! isset($wPoints[$j]->ontrack) && round($wPoints[$j]->lat, 3)==round($tPoint->lat, 3) && round($wPoints[$j]->lon, 3)==round($tPoint->lon, 3)) {
                    $wPoints[$j]->ontrack = true;                       // OK We set this to avoid rechecking later 
                    $k = explode(' ', $wPoints[$j]->name);              // Skip automated Start & Finish waypoints
                    if ($k[0]=='Start' || $k[0]=='Finish') { continue; }
                    $wPoints[$j]->d = round($d/$m, 3);                  // Set Waypoint distance same as current point's distance
                    $data3[] = ['x' => round($d/$m, 3), 'y' => round($y, 1), 'l' => $wPoints[$j]->name.' ('.$wPoints[$j]->osref.')'];
                }    
            }
        }

//      dd($wPoints, $data3);
        $minY = $minY<=10 ? 0 : $minY - 10;
        $minY = ceil($minY / 10) * 10;
        $maxY = ceil(($maxY + 10) / 10) * 10;
        $range = [0, round($d/$m, 2), $minY, $maxY, round($c, 1)];

        // Lets run the route again and check if there are any waypoints that could be a little further from the track
        // We use a rounding of 2 dec places instead of 3 to do this. We also allow multiple points on the track if not within 200m 
        $c  = 0.0;                              // Elevation
        $d  = 0.0;                              // Distance
        for ($i=0; $i<sizeof($tPoints); ++$i) {
            $tPoint = $tPoints[$i];
            $x = isset($tPoint->d  ) ? $tPoint->d   : 0;                // delta Distance
            $y = isset($tPoint->ele) ? $tPoint->ele : 0;                // Elevation
            $d = $d + $x;                                               // Total Distance
            if($i>0 && $y>$eleOld) { $c = $c + $y - $eleOld; }          // Total Climb
            $eleOld = $tPoint->ele;

            for ($j=0; $j<sizeof($wPoints); ++$j) {                     // Here we check if our Waypoints are nearby to our track 
                if (! isset($wPoints[$j]->ontrack) && round($wPoints[$j]->lat, 2)==round($tPoint->lat, 2) && round($wPoints[$j]->lon, 2)==round($tPoint->lon, 2)) {
                    if (isset($wPoints[$j]->d2)) {
                        $d2 = OSRefs([[$wPoints[$j]->east, $wPoints[$j]->north], $wPoints[$j]->d2])[1]->d;
                        if ($d2<=200) { continue; }
                    }
                    //$wPoints[$j]->ontrack = true;                     // OK We set this to avoid rechecking later 
                    $wPoints[$j]->d = round($d/$m, 3);                  // Set Waypoint distance same as current point's distance
                    $wPoints[$j]->d2 = [$tPoint->east, $tPoint->north]; // Set Waypoint distance same as current point's distance
                    $data3[] = ['x' => round($d/$m, 3), 'y' => round($y, 1), 'l' => $wPoints[$j]->name.' ('.$wPoints[$j]->osref.')'];
                }    
            }
        }

        return json_encode(['data1' => $data1, 'data2' => $data2, 'data3' => $data3, 'data4' => $data4,
            'data5' => $data5, 'data6' => $data6, 'range' => $range]);   
    }    

}
