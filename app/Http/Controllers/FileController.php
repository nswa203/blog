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
            'searchModel'   => ['title', 'file'],
            'searchRelated' => [
                'folder'  => ['name', 'slug', 'description'],
                'tags'    => ['name'],
            ]
        ];
        return search_helper($search, $query);
    }

    // $list['o'] for options
    public function fileOption($default = -1) {
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

    // $list['d'] for folders
    public function folderStatus($default = -1) {
        $status = [
            '1' => 'Public',
            '0' => 'Private',
        ];
        if ($default >= 0) { $status[$default] = '*' . $status[$default]; }
        return $status;
    }

    // $list['f'] for files
    public function fileStatus($default = -1) {
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $files = $this->searchQuery($request->search)->with('folder')->orderBy('id', 'desc')->paginate(10);
        $list['f'] = $this->fileStatus();
        $list['d'] = $this->folderStatus();
        if ($files && $files->count() > 0) {

        } else {
            Session::flash('failure', 'No Files were found.');
        }
        return view('manage.files.index', ['files' => $files, 'search' => $request->search, 'list' => $list]);
     }
    public function indexOf(Request $request, $folder_id) {
        $files = $this->searchQuery($request->search)->with('folder')->where('folder_id', $folder_id)->orderBy('id', 'desc')->paginate(10);
        $list['f'] = $this->fileStatus();
        $list['d'] = $this->folderStatus();   
        if ($files && $files->count() > 0) {

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
        $list['f'] = $this->fileStatus(2);
        $list['o'] = $this->fileOption(3);        
        $mimes = 'audio/*,video/*,image/*,.pdf,.txt,.log';

        return view('manage.files.create', ['file' => $file, 'folders' => $folders, 'tags' => $tags,
            'mimes' => $mimes, 'list' => $list, 'folder_id' => null]);
    }
    public function createIn($folder_id)
    {
        $folder = Folder::findOrFail($folder_id);
        $folders = [$folder->id => $folder->slug];
        $tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');
        $file = new File;
        $list['f'] = $this->fileStatus(2);
        $list['o'] = $this->fileOption(3);
        $mimes = 'audio/*,video/*,image/*,.pdf,.txt,.log';

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
        $this->validate($request, [
            'title'     => 'sometimes|max:191',
            'files'     => 'required|array|between:1,64',
            'files.*'   => 'filled|max:10000000|mimetypes:audio/*,video/*,image/*,application/pdf,text/plain,application/octet-stream',
            'status'    => 'required|integer|min:0|max:4',
            'option'    => 'required|integer|min:0|max:4',
            'folder_id' => 'required|integer|exists:folders,id',
            'tags'      => 'array',
            'tags.*'    => 'integer|exists:tags,id',
        ]);

        $files        = Input::file('files');
        $folder       = folderWithSize($request->folder_id);                                // Get Folder:: with refreshed size
        $countBad     = count($files);
        $count        = 0;

        foreach ($files as $item) {
            $count++;
            $file = new File;
            $file->title        = $request->title ? $request->title . '.%baseext%' : ($request->option == 2 ? '%basename% %date% %time%.%baseext%' :'%basename%.%baseext%');
            $file->status       = $request->status;
            $file->folder_id    = $request->folder_id;
            $file->size         = $item->getSize();
            $file->published_at = $file->status == '4' ? date('Y-m-d H:i:s') : null;

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
            $list['f'] = $this->fileStatus();
            $list['d'] = $this->folderStatus();
        if ($file) {
            return view('manage.files.show', ['file' => $file, 'list' => $list]);
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


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\File  $file
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
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
}
