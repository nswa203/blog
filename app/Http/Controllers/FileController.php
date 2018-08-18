<?php

namespace App\Http\Controllers;

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
    // It requires the custom search_helper() function in Helpers.php.
    // If you change Helpers.php you should do "dump-autoload". 
    public function searchQuery($search = '') {
        $query = [
            'model'         => 'File',
            'searchModel'   => ['title', 'file', 'mime_type', 'meta'],
            'searchRelated' => [
                'folder'  => ['name', 'slug', 'description'],
                'tags'    => ['name'],
            ]
        ];
        return search_helper($search, $query);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $pager = pageSize($request, 'filesIndex', 10, 5, 200, 5);    // size($request->pp), sessionTag, default, min, max, step
        $files = $this->searchQuery($request->search)->with('folder')->orderBy('id', 'desc')->paginate($pager['size']);
        $files->pager = $pager;

        $list['f'] = fileStatus();
        $list['d'] = folderStatus();

        if ($files && $files->count() > 0) {
            $x = $this->searchQuery($request->search)->with('folder')->orderBy('id', 'desc')->pluck('id')->toArray();
            mySession('filesIndex', 'index', $x);
            mySession('filesShow', 'indexURL', $request->url().'?'.$request->getQueryString());
        } else {
            Session::flash('failure', 'No Files were found.');
        }
        return view('manage.files.index', ['files' => $files, 'search' => $request->search, 'list' => $list]);
     }
    public function indexOf(Request $request, $folder_id) {
        $pager = pageSize($request, 'filesIndexOf', 20, 5, 200, 10);    // model, size($request->pp), min, max, step
        $files = $this->searchQuery($request->search)->with('folder')->where('folder_id', $folder_id)
            ->orderBy('id', 'desc')->paginate($pager['size']);
        $files->pager = $pager;

        $list['f'] = fileStatus();
        $list['d'] = folderStatus();   
        
        if ($files && $files->count() > 0) {
            $x = $this->searchQuery($request->search)->with('folder')->where('folder_id', $folder_id)
                ->orderBy('id', 'desc')->pluck('id')->toArray();
            mySession('filesIndex', 'index', $x);
            mySession('filesShow', 'indexURL', $request->url().'?'.$request->getQueryString());
        } else {
            Session::flash('failure', 'No Files were found.');
        }
        return view('manage.files.index', ['files' => $files, 'search' => $folder_id, 'list' => $list]);
     }      

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $folders = Folder::orderBy('slug', 'asc')->pluck('slug', 'id');
        $tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');
        $file = new File;
        $list['f'] = fileStatus(2);
        $list['o'] = fileOption(3);        
        $mimes = 'text/*,image/*,audio/*,video/*,.pdf,.txt,.log,.ico,.nfo,.nft,.srt,.rex,.rexx,.bat,.cmd,.php,.js,.rar,.zip';

        return view('manage.files.create', ['file' => $file, 'folders' => $folders, 'tags' => $tags,
            'mimes' => $mimes, 'list' => $list, 'folder_id' => null]);
    }
    public function createIn($folder_id)
    {
        $folder = Folder::findOrFail($folder_id);
        $folders = [$folder->id => $folder->slug];
        $tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');
        $file = new File;
        $list['f'] = fileStatus(2);
        $list['o'] = fileOption(3);
        $mimes = 'text/*,image/*,audio/*,video/*,.pdf,.txt,.log,.ico,.nfo,.nft,.srt,.rex,.rexx,.bat,.cmd,.php,.js,.rar,.zip';

        return view('manage.files.create', ['file' => $file, 'folders' => $folders, 'tags' => $tags,
            'mimes' => $mimes, 'list' => $list, 'folder_id' => $folder_id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $files = Input::file('files');              // Provide Mime info msgs just in case validate triggers a failure  
        for ($i=0; $i<count($files); $i++) {
            $msg = 'MimeType File.' . $i . ': ' . $files[$i]->getMimeType();
            msgx(['info' => [$msg, true]]);
        }
        $this->validate($request, [
            'title'     => 'sometimes|max:191',
            'files'     => 'required|array|between:1,64',
            'files.*'   => 'filled|max:10000000|mimetypes:audio/*,video/*,image/*,application/pdf,text/plain,application/octet-stream,application/zip,text/html,application/x-7z-compressed,text/x-php,text/x-msdos-batch,text/xml',
            'status'    => 'required|integer|min:0|max:4',
            'option'    => 'required|integer|min:0|max:4',
            'folder_id' => 'required|integer|exists:folders,id',
            'tags'      => 'array',
            'tags.*'    => 'integer|exists:tags,id',
        ]);
        Session::put('msgx', []);                   // If we got here validate worked so remove info msgs 

        //$files        = Input::file('files');
        $folder       = folderWithSize($request->folder_id);                                // Get Folder:: with refreshed size
        $countBad     = count($files);
        $count        = 0;

        foreach ($files as $item) {
            $count++;
            $file = new File;
            $file->title        = $request->title ? $request->title :
                ($request->option == 2 ? '%basename% %date% %time%.%baseext%' :'%basename%.%baseext%');
            $file->status       = $request->status;
            $file->folder_id    = $request->folder_id;
            $file->size         = $item->getSize();
            $file->published_at = $file->status == '4' ? date('Y-m-d H:i:s') : null;
            $file->mime_type    = $item->getMimeType();
            $fileName = $item->getClientOriginalName();                                     // fn.ft
            $fileWrap = myTrim($fileName, 48);                                              // fn... ft
            $filePath = folder_path($folder)->path . '\\' . $fileName;                      // C:\folder\fn.ft
            $pathName = pathinfo($fileName, PATHINFO_FILENAME);                             // fn
            $pathExt  = pathinfo($fileName, PATHINFO_EXTENSION);                            // ft

            // Replacements ------------------------------------------------------------------------------------------
            $needles = ['%title%', '%filename%', '%basename%', '%baseext%', '%size%', '%folder%', '%date%', '%time%'];

            $replace = [$file->title, $fileName, $pathName, $pathExt, $file->size, $folder->name, date('Y-m-d'), date('H-m-s') ];
            $file->title = str_replace($needles, $replace, $file->title);
            $folder_used = ($folder->size)             / $folder->max_size / 1048576 * 100;
            $folder_want = ($folder->size+$file->size) / $folder->max_size / 1048576 * 100;

            // Check for sufficient disk space -----------------------------------------------------------------------
            if ($folder_want > 100) {
                $myrc = false;
                $msg = 'Folder "'.$folder->name.'" is '.round($folder_used, 2).'% full.'.' Space requested was '.round($folder_want, 2).'%'; 
                msgx(['info' => [$msg, !$myrc]]);
                $msg = 'File '.$count.': "'.$fileWrap.'" out of space.';
                msgx(['warning' => [$msg, !$myrc]]);
                break;
            }

            // Check for existing file with same name ----------------------------------------------------------------
            $myrc = FileSys::exists($filePath); 
            if ($myrc) {
                $msg = 'File '.$count.': "'.$fileWrap.'" already exists in Folder "'.$folder->name.'".';
                if     ($request->option == 0) { msgx(['warning' => [$msg, true]]); break; }
                elseif ($request->option == 1) { msgx(['info'    => [$msg, true]]); continue; }
                elseif ($request->option == 2) { $fileNameAlt = $file->title.'.'.$pathExt; }
                elseif ($request->option == 3) {
                    for ($i=1; $i<=100; $i++) {
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
                    $fileWrapAlt = myTrim($fileNameAlt, 48);
                    $msg = 'File '.$count.': "'.$fileWrapAlt.'" already exists in Folder "'.$folder->name.'".';
                    $filePath = folder_path($folder)->path.'\\'.$fileNameAlt;
                    if (FileSys::exists($filePath)) { msgx(['warning' => [$msg, true]]); break; }    
                    $msg = 'File '.$count.': "'.$fileWrap.'" auto renamed to "'.$fileWrapAlt.'".';
                    msgx(['info' => [$msg, true]]);                        
                }
            }                

            // Extract meta data from file ----------------------------------------------------------------------------
            $file->meta = getMeta($item);

            // Save the file
            $myrc = FileSys::copy($item, $filePath);
            if ($myrc) { 
                $file->file = basename($filePath);
                $file->save();
                $file->tags()->sync($request->tags, false);
                $folder->size = $folder->size + $file->size;
                $countBad--;
                $msg = 'File '.$count.': saved as "'.myTrim($file->file, 48).'".';
                msgx(['info' => [$msg, true]]);                        
            }

        } // EndForEach

        // Update Folder:: 
        folderWithSize($folder, true);      // ...updating Folder Size

        if ($myrc && $countBad == 0) {
            Session::flash('success', 'All Files were successfully saved.');
            return redirect()->route('files.indexOf', $folder->id);
        } else {
            $count = count($files);
            Session::flash('failure',
                $countBad == $count ? 'No Files were saved.' : $countBad.' of '.$count.' Files were NOT saved.');
            return Redirect::back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $file = File::where('id', $id)->with('folder')->first();
        if ($file) {
            $file->ext = pathinfo($file->file, PATHINFO_EXTENSION);
            $list['f'] = fileStatus();
            $list['d'] = folderStatus();
            $list['x'] = showNav($id, mySession('filesIndex', 'index'));     // Build list of First, Previous, Next, Last

            return view('manage.files.show', ['file' => $file, 'meta' => json_decode($file->meta), 'list' => $list, 'search' => true]);
        } else {
            Session::flash('failure', 'File "' . $id . '" was NOT found.');
            return Redirect::back();
        }
    }

    public function showFile($id)
    {
        $file = File::where('id', $id)->with('folder')->first();
        if ($file) {
            $file->ext = pathinfo($file->file, PATHINFO_EXTENSION);
            $list['x'] = showNav($id, mySession('filesIndex', 'index'));     // Build list of First, Previous, Next, Last

            return view('manage.files.showImage', ['file' => $file, 'meta' => json_decode($file->meta), 'list' => $list, 'search' => true]);
        } else {
            Session::flash('failure', 'File "' . $id . '" was NOT found.');
            return Redirect::back();
        }
    }


    public function getFile($id) {
        $myrc = false;
        $file = File::findOrFail($id);
        $path = filePath($file);
        if (File::exists($path)) {
            $file = FileSys::get($path);
            $type = FileSys::mimeType($path);
            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);
            $myrc = true;
        } 
   
        if ($myrc) { return $response; }
        else { abort(404); }
    }

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
        else { abort(404); } 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $file = File::findOrFail($id);
        if ($file) {
            $folders = Folder::orderBy('slug', 'asc')->pluck('slug', 'id');
            $tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');
            $mimes = 'audio/*,video/*,image/*,.pdf,.txt,.log';
            $list['f'] = fileStatus();
            $list['o'] = fileOption(3);
            $list['d'] = folderStatus();
            return view('manage.files.edit', ['file' => $file, 'folders' => $folders, 'tags' => $tags,
                'mimes' => $mimes, 'list' => $list, 'folder_id' => null]);
        } else {
            Session::flash('failure', 'File "' . $id . '" was NOT found.');
            return Redirect::back();
        }
    }

    /**
     * mixed router for file.show|edit|delete
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function mixed(Request $request)
    {
        $choice = explode(',', $request->choice);
        if ($choice[0] == 'delete') {

        } elseif ($choice[0] == 'edit') {

        } else {
            if ($request->itemsSelected) {
                $ids = explode(',', $request->itemsSelected);
                mySession('filesIndex', 'index', $ids);
                $id = $ids[0]; 
            }
            else { $id = $choice[1]; }    
            return redirect()->route('files.show', $id);    
        }

        $ids = $request->itemsSelected ? $request->itemsSelected : $choice[1];
        dd($request, $choice[0].': '.$ids);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        //
    }


   /**
     * API Change Paginator per page count
     */
    public function apiPageCount(Request $request) {
        $slug = $request->slug;
        
        return json_encode('OK');
    }

}
