<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File as FileSys;
use App\Folder;
use App\Category;
use App\Post;
use App\Profile;
use App\User;
use Auth;
use Image; 
use Purifier;
use Response;
use Session;
use Validator;

class FolderController extends Controller
{

    /**
     * Use middleware to set up common constants and secure methods by name
     *
     * @param  Illuminate\Http\Request $request
     * @param  Illuminate\Http\Route $next
     * @return Illuminate\Http\Route;
     */
    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            $folder = $request->route('folder') ? Folder::find($request->route('folder')) : false;
            $owner_id = $folder ? $folder->user_id : '*';

            if (! permit($this->permits($owner_id))) {
                Session::flash('failure', "It doesn't look like you have permission for that action!");
                return redirect(previous());
            }

            // This codes extends validation to ensure restricted users may only 
            // Store & Update resources that they own   
            if (permit($this->permits(), 'updateAll')) { 
                Validator::extend('ownUser', function() { return true; });
            } else {
                Validator::extend('ownUser', function() use ($request) {
                    return $request->user_id==auth()->user()->id; },
                    "Invalid User."
                );
            }

            if (permit($this->permits(), 'setSize')) { 
                Validator::extend('canSetSize', function() { return true; });
            } else {
                Validator::extend('canSetSize', function() use ($request) {
                    return $request->max_size<=250; },
                    "Invalid Size - The maximum that you may set is 250MB."
                );
            }  

            session(['zone' => 'Folders']);                     // Set the active zone for search()
            previous(url($request->getPathInfo()));             // Set the previous url for redirect(previous()) 
            return $next($request);
        });
    }

    // These permits are used by permit() in the __contruct() middleware to secure the controller actions 
    // This could be done in the Route config - but it seems to make more sense to do it in the controller.
    // $owner_id='*' permits all Users for a permission   
    public function permits($owner_id='^') {
        $permits = [
            'showAll'        => 'permission:folders-read',
            'index'          => 'permission:folders-read,owner:'.$owner_id.'|folders-read-ifowner',
            'show'           => 'permission:folders-read,owner:'.$owner_id.'|folders-read-ifowner',
            'create'         => 'permission:folders-create|folders-create-ifowner',
            'store'          => 'permission:folders-create|folders-create-ifowner',
            'edit'           => 'permission:folders-update,owner:'.$owner_id.'|folders-update-ifowner',
            'updateAll'      => 'permission:folders-update',
            'update'         => 'permission:folders-update,owner:'.$owner_id.'|folders-update-ifowner',
            'delete'         => 'permission:folders-delete,owner:'.$owner_id.'|folders-delete-ifowner',
            'destroy'        => 'permission:folders-delete,owner:'.$owner_id.'|folders-delete-ifowner',
            'apiCheckUnique' => 'user:*',
            'getFolderFile'  => 'permission:folders-read,owner:'.$owner_id.'|folders-read-ifowner',
            'setSize'        => 'permission:foldersizes-update',
            'default'        => '' 
        ];
        return $permits;
    }

    /**
     * Used by the custom queryHelper() function in Helpers.php to
     * search table/columns and related_tables/columns for each word/phrase.
     * The table is filtered and finally sorted (ascending or descending).
     *
     * @param  App\User $user_id
     * @return Array $query
     */
    public function query($user_id=false) {
        $query = [          // Enclose a search column in "^" for exact match
            'model'         => 'Folder',
            'searchModel'   => ['name', 'slug', 'directory', 'description', 'image'],
            'searchRelated' => [
                'category' => ['name'],
                'posts'    => ['title', 'body', 'excerpt'],
                'profiles' => ['^username^', 'about_me', 'phone', 'address'],
                'user'     => ['^name^', '^email^']
            ],
            'sort'   => [ // (a,d) default sort order  
                'i'       => 'd,id',                                                      
                'n'       => 'a,name',
                's'       => 'a,slug',                                           
                'd'       => 'a,description',                                            
                'm'       => 'd,max_size',
                'u'       => 'd,updated_at',
                'f'       => 'a,status',
                'c'       => 'a,name,category',
                'o'       => 'a,name,user',
                'default' => 'i' // default sort column                       
            ],
            'filters'    => [
                //['status', '=', '1']
            ]                                    
        ];
        if ($user_id) {
            $query['filters'][] = ['user_id', '=', $user_id];
        }
        return $query;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Support\Facades\Redirect;
     */
    public function index(Request $request)
    {
        $id = permit($this->permits(), 'showAll') ? '' : Auth::user()->id;
        $folders = paginateHelper($this->query($id), $request, 12, 4, 192, 4); // size($request->pp), default, min, max, step

        if ($folders && $folders->count()>0) {

        } else {
            Session::flash('failure', 'No Folders were found.');
        }

        $list['d'] = folderStatus();
        return view('manage.folders.index', ['folders' => $folders, 'search' => $request->search, 'sort' => $request->sort,
            'list' => $list]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Illuminate\Support\Facades\Redirect;
     */
    public function create() {
        $categories = Category::orderBy('name', 'asc')->pluck('name', 'id');
        $folder = new Folder;
        $folder->max_size = '250';
        $list['d'] = folderStatus(0);
        $mimes = 'image/*,.ico';

        if (permit($this->permits(), 'updateAll')) {
            $users = User::orderBy('name', 'asc')->pluck('name', 'id');            
        } else {
            $users = [Auth::user()->id => Auth::user()->name];            
        }

        return view('manage.folders.create', ['folder' => $folder, 'categories' => $categories, 'users' => $users,
            'mimes' => $mimes, 'list' => $list]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Support\Facades\Redirect;
     */
    public function store(Request $request) {
        // We use our own validator here as auto validation cannot handle error return from a POST method 
        $validator = Validator::make($request->all(), [
            'title'             => 'required|min:3|max:191',
            'slug'              => 'required|alpha_dash|min:3|max:191|unique:folders,slug',
            'description'       => 'sometimes|max:2048',
            'image'             => 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:10000|min:1',
            'max_size'          => 'required|integer|min:1|max:16000|canSetSize',
            'category_id'       => 'required|integer|exists:categories,id',
            'user_id'           => 'sometimes|integer|exists:users,id|ownUser',
            'status'            => 'required|integer|min:0|max:1',
        ]);

        if ($validator->fails()) {
            return redirect()->route('folders.create')->withErrors($validator)->withInput();
        }        

        $folder = new Folder;
        $folder->name             = $request->title;
        $folder->slug             = $request->slug;
        $folder->description      = Purifier::clean($request->description);
        $folder->max_size         = $request->max_size;
        $folder->category_id      = $request->category_id;
        $folder->user_id          = $request->user_id ? $request->user_id : auth()->user()->id;
        $folder->status           = $request->status;
        
        $user = User::find($folder->user_id);
        $profile = Profile::find($user->profile['id']);

        // Create the directory - either Public or Private
        if ($folder->status == 1) {
            $msg = 'Public ';
            $folder->directory = 'folders\\' . $folder->slug;
            $path = public_path($folder->directory);
            $myrc = FileSys::makeDirectory($path, 0666, true, true);
        } else {
            $msg = 'Private ';
            $folder->directory = 'folders\\' . $user->name . '\\' . $folder->slug;
            $path = private_path($folder->directory);
            $myrc = FileSys::makeDirectory($path, 0660, true, true);
        }

        $msg = $msg . 'Directory "' . $folder->directory . '" was successfully created.';
        msgx(['info' => [$msg, $myrc]]);

        // Process the Folder cover image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'Folder.jpg';             // $image->getClientOriginalExtension();
            $location = $path . '\\' . $filename;
            $myrc = Image::make($image)->widen(800, function ($constraint) { $constraint->upsize(); })->save($location);
            $folder->image = $filename;
            $msg = 'Image "' . $image->getClientOriginalName() . '" was successfully saved as ' . $filename;
            msgx(['info' => [$msg, $myrc != null]]);
        }

        //$folder->size = folderSize($path);
        $myrc = $folder->save();
        
        if ($myrc) {
            if ($profile) { $myrc = $profile->folders()->sync($folder->id, false); }
            Session::flash('success', 'Folder "' . $folder->slug . '" was successfully saved.');
            return redirect()->route('folders.show', $folder->id);
        } else {
            Session::flash('failure', 'Folder "' . $id . '" was NOT saved.');
            return redirect()->route('folders.create')->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Illuminate\Http\Request $request
     * @param  App\Folder $id
     * @return Illuminate\Support\Facades\Redirect;
     */
    public function show(Request $request, $id) {
        $folder = Folder::find($id);

        if ($folder) {
            $context = app('request')->route()->getAction('as');
            $pager = pageSize($request, 'folderShow', 5, 5, 200, 5);    // model, size($request->pp), min, max, step
            $files = $folder->files()->orderBy('title', 'asc')->paginate($pager['size'], ['*'], 'pageFi');
            $files->pager = $pager;

            $posts = $folder->posts()->orderBy('slug',  'asc')->paginate($pager['size'], ['*'], 'pagePo');
            $posts->pager = $pager;

            $profiles = $folder->profiles()->with('user')->orderBy('username', 'asc')->paginate($pager['size'], ['*'], 'pagePr');
            $profiles->pager = $pager;

            if ($folder->status == 0) { $folder->path = private_path($folder->directory); }
            else                      { $folder->path =  public_path($folder->directory); }
            $list['d'] = folderStatus();
            $list['f'] = fileStatus();
            $x = $folder->files()->orderBy('title', 'asc')->pluck('id')->toArray();
            mySession('filesIndex', 'index', $x);
            mySession('filesShow', 'indexURL', $request->url().'?'.$request->getQueryString());
            return view('manage.folders.show', ['folder' => $folder, 'files' => $files, 'posts' => $posts, 'profiles' => $profiles,
                'list' => $list]);
        } else {
            Session::flash('failure', 'Folder "' . $id . '" was NOT found.');
            return redirect(previous());
            return redirect()->route('folders.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  App\Folder $id
     * @return Illuminate\Support\Facades\Redirect;
     */
    public function edit($id) {
        $folder = Folder::find($id);

        if ($folder) {
            $categories = Category::orderBy('name', 'asc')->pluck('name', 'id');
            $folder->title = $folder->name;
            $list['d'] = folderStatus();

            if (permit($this->permits(), 'updateAll')) {
                $users = User::orderBy('name', 'asc')->pluck('name', 'id');            
            } else {
                $users = [Auth::user()->id => Auth::user()->name];            
            }

            return view('manage.folders.edit', ['folder' => $folder, 'categories' => $categories, 'users' => $users, 'list' => $list]);
        } else {
            Session::flash('failure', 'Folder "' . $id . '" was NOT found.');
            return redirect(previous());
            return redirect()->route('folders.index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Illuminate\Http\Request $request
     * @param  App\Folder $id
     * @return Illuminate\Support\Facades\Redirect;
     */
    public function update(Request $request, $id) {
        $folder = Folder::findOrFail($id);

        // We use our own validator here as auto validation cannot handle error return from a POST method 
        $validator = Validator::make($request->all(), [
            'title'             => 'sometimes|min:3|max:191',
            'slug'              => 'sometimes|alpha_dash|min:3|max:191|unique:folders,slug,' . $id,
            'description'       => 'sometimes|max:2048',
            'image'             => 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:10000|min:1',
            'max_size'          => 'sometimes|integer|min:1|max:16000|canSetSize',
            'category_id'       => 'sometimes|integer|exists:categories,id',
            'user_id'           => 'sometimes|integer|exists:users,id|ownUser',
            'status'            => 'sometimes|integer|min:0|max:1',
        ]);

        if ($validator->fails()) {
            return redirect()->route('folders.edit', [$id])->withErrors($validator)->withInput();
        }        

        $folder->description = isset($request->description) ? Purifier::clean($request->description) : $folder->description;
        $folder->max_size    = isset($request->max_size)    ? $request->max_size                     : $folder->max_size;
        $folder->name        = isset($request->title)       ? $request->title                        : $folder->name;
        $folder->category_id = isset($request->category_id) ? $request->category_id                  : $folder->category_id;
        $new_status          = isset($request->status)      ? $request->status                       : $folder->status;
        $new_slug            = isset($request->slug)        ? $request->slug                         : $folder->slug;
        $new_user_id         = isset($request->user_id)     ? $request->user_id                      : $folder->user_id;

        // Move the folder if required
        if ($folder->status . $folder->slug . $folder->user_id != $new_status . $new_slug . $new_user_id) {
            
            if ($folder->status == 1) {
                $old_directory = 'folders\\' . $folder->slug;
                $old_path = public_path($old_directory);
            } else {
                $user = User::find($folder->user_id);
                $old_profile = Profile::find($user->profile['id']);
                $old_directory = 'folders\\' . $user->name . '\\' . $folder->slug;
                $old_path = private_path($old_directory);
            }            
            
            if ($new_status == 1) {
                $new_directory = 'folders\\' . $new_slug;
                $new_path = public_path($new_directory);
            } else {
                $user = User::find($new_user_id);
                $new_profile = Profile::find($user->profile['id']);
                $new_directory = 'folders\\' . $user->name . '\\' . $new_slug;
                $new_path = private_path($new_directory);
            }

            if ($old_path == $new_path) {
                $myrc = true;
            } elseif (FileSys::exists($old_path) && ! FileSys::exists($new_path)) {
                $myrc = FileSys::copyDirectory($old_path, $new_path);
                if ($myrc) { $myrc = FileSys::deleteDirectory($old_path); }
            } else {
                $myrc = false;
            }
            
            if ($myrc) {
                $folder->status    = $new_status;
                $folder->slug      = $new_slug;
                $folder->user_id   = $new_user_id;
                $folder->directory = $new_directory;                
                $msg = 'Directory "' . $old_directory . '" was successfully moved to "' . $new_directory . '".';
                msgx(['info' => [$msg, $myrc]]);
            } else { 
                $msg = 'Directory "' . $old_directory . '" could not be moved!';
                msgx(['failure' => [$msg, $myrc]]);
            }    
        }

        $path = $folder->status == 1 ? public_path($folder->directory) : private_path($folder->directory);    

        // Process the Folder cover image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'Folder.jpg';
            $location = $path . '\\' . $filename;
            $myrc = Image::make($image)->widen(800, function ($constraint) { $constraint->upsize(); })->save($location);
            $folder->image = $filename;
            $msg = 'Image "' . $image->getClientOriginalName() . '" was successfully saved as ' . $filename;
            msgx(['info' => [$msg, $myrc != null]]);
        } elseif ($request->delete_image) {
            $filename = 'Folder.jpg';
            $location = $path . '\\' . $filename;
            $myrc = FileSys::delete($location);
            $folder->image = null;
            $msg = 'Image "' . $filename . '" has been deleted.';
            msgx(['info' => [$msg, $myrc != null]]);
        }

        //$folder->size = folderSize($path);

        $myrc = $folder->save();
        
        if ($myrc) {
            if (isset($old_profile)) { $myrc = $old_profile->folders()->detach($folder->id); }
            if (isset($new_profile)) { $myrc = $new_profile->folders()->sync($folder->id, true); }
            Session::flash('success', 'Folder "' . $folder->slug . '" was successfully saved.');
            return redirect()->route('folders.show', $folder->id);
        } else {
            Session::flash('failure', 'Folder "' . $id . '" was NOT saved.');
            return redirect()->route('folders.edit')->withInput();
        }
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param  App\Folder $id
     * @return Illuminate\Support\Facades\Redirect;
     */
    public function delete($id) {
        $folder = Folder::find($id);

        if ($folder) {
            $list['d'] = folderStatus();
            return view('manage.folders.delete', ['folder' => $folder, 'list' => $list]);
         } else {
            Session::flash('failure', 'Folder "' . $id . '" was NOT found.');
            return redirect(previous());
            return redirect()->route('folders.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Illuminate\Http\Request $request
     * @param  App\Folder $id
     * @return Illuminate\Support\Facades\Redirect;
     */
    public function destroy(Request $request, $id) {
        $folder = Folder::findOrFail($id);

        $myrc = $folder->delete();

        if ($myrc) {
            if ($folder->status == 1) {
                $msg = 'Public';
                $path = public_path($folder->directory);
            } else {
                $msg = 'Private';
                $path = private_path($folder->directory);
            }

            $myrc = FileSys::deleteDirectory($path);
            
            if ($myrc) {
                $msg = $msg . ' Directory "' . $folder->directory . '" was successfully deleted.';
                msgx(['Info:' => [$msg, true]]);
            } else {
                $msg = $msg . ' Directory "' . $folder->directory . '" could NOT be deleted.';
                msgx(['failure' => [$msg, true]]);                    
            }
            
            Session::flash('success', 'Folder "' . $folder->name . '" deleted OK.');
            return redirect()->route('folders.index');
        } else {
            Session::flash('failure', 'Folder "' . $id . '" was NOT deleted.');
            return redirect()->route('folders.delete', $id)->withinput();
        }
    }

    /**
     * Return an inline version of the specified resource.
     * Retrieves a file by folder->id + filename rather than file->id
     *
     * @param  App\Folder $id
     * @param  App\File $filename
     * @return Illuminate\Http\Response
     */
    public function getFolderFile($id, $filename='Folder.jpg') {
        $myrc = false;
        $folder = Folder::findOrFail($id);
        $path = folderPath($folder) . '\\' . $filename;;
        
        if (FileSys::exists($path)) {
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
     * API Check unique
     * We check if exists & modify it if it does. 
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response 
     */
    public function apiCheckUnique(Request $request) {
        $slug = $request->slug;
        for ($i=1; $i<100; $i++) {
            
            if (! Folder::where('slug', '=', $slug)->exists()) {
                break;
            } else {
                
                if ($request->id) {
                    $folder = Folder::find($id);
                    
                    if ($folder->slug == $slug) {
                        break;
                    }   
                }

                $slug = $request->slug . '-' . $i;
            } 
        }
        return json_encode(substr($slug, 0, 64));
    }

}
