<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Folder;
use App\Category;
use App\Post;
use App\Profile;
use App\User;
use Session;
use Purifier;
use Image; 
use File;
use Storage;
use Response;

class FolderController extends Controller
{

    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            session(['zone' => 'Folders']);
            return $next($request);
        });
    }

    // This Query Builder searches our table/columns and related_tables/columns for each word/phrase.
    // It requires the custom search_helper() function in Helpers.php.
    // If you change Helpers.php you should do "dump-autoload". 
    public function searchQuery($search = '') {
        $query = [
            'model'         => 'Folder',
            'searchModel'   => ['name', 'slug', 'directory', 'description', 'image'],
            'searchRelated' => [
                'category' => ['name'],
                'posts'    => ['title', 'body', 'excerpt'],
                'profiles' => ['username', 'about_me', 'phone', 'address'],
                'user'     => ['name', 'email']
            ]
        ];
        return search_helper($search, $query);
    }

    // $status_list
    public function status($default = -1) {
        $status = [
            '1' => 'Public',
            '0' => 'Private',
        ];
        if ($default >= 0) { $status[$default] = '*' . $status[$default]; }
        return $status;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $folders = $this->searchQuery($request->search)->orderBy('name', 'asc')->paginate(10);
        if ($folders) {

        } else {
            Session::flash('failure', 'No Folders were found.');
        }
        return view('manage.folders.index', ['folders' => $folders, 'search' => $request->search, 'status_list' => $this->status()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $categories = Category::orderBy('name', 'asc')->pluck('name', 'id');
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');
        $folder = new Folder;

        return view('manage.folders.create', ['folder' => $folder,
            'categories' => $categories, 'users' => $users, 'status_list' => $this->status(0)]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'title'             => 'required|min:3|max:191',
            'slug'              => 'required|alpha_dash|min:3|max:191|unique:albums,slug',
            'description'       => 'sometimes|max:2048',
            'image'             => 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:10000|min:1',
            'max_size'          => 'required|integer|min:0|max:16000',
            'category_id'       => 'required|integer|exists:categories,id',
            'user_id'           => 'sometimes|integer|exists:users,id',
            'status'            => 'required|integer|min:0|max:1',
        ]);

        $folder = new Folder;
        $folder->name             = $request->title;
        $folder->slug             = $request->slug;
        $folder->description      = Purifier::clean($request->description);
        $folder->max_size         = $request->max_size*1000000;
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
            $myrc = File::makeDirectory($path, 0666, true, true);
        } else {
            $msg = 'Private ';
            $folder->directory = 'folders\\' . $user->name . '\\' . $folder->slug;
            $path = private_path($folder->directory);
            $myrc = File::makeDirectory($path, 0660, true, true);
        }
        $msg = $msg . 'Directory "' . $folder->directory . '" was successfully created.';
        msgx(['info' => [$msg, $myrc]]);

        // Process the Folder cover image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'Folder.' . $image->getClientOriginalExtension();
            $location = $path . '\\' . $filename;
        //dd($request, $image, $filename, $path, $location);    
    
            $myrc = Image::make($image)->widen(800, function ($constraint) { $constraint->upsize(); })->save($location);
            $folder->image = $filename;
         //dd(Storage::setVisibility($location, 'public'));  
            $msg = 'Image "' . $image->getClientOriginalName() . '" was successfully saved as ' . $filename;
            msgx(['info' => [$msg, $myrc!=null]]);
        }

        $folder->size = folderSize($path);
        $myrc = $folder->save();
        
        if ($myrc) {
            if ($profile) { $myrc = $profile->folders()->sync($folder->id, false); }
            Session::flash('success', 'Folder "' . $folder->slug . '" was successfully saved.');
//          return redirect()->route('folders.show', $folder->id);
            return redirect()->route('folders.create')->withInput();
        } else {
            Session::flash('failure', 'Folder "' . $id . '" was NOT saved.');
            return redirect()->route('folders.create')->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $folder = Folder::findOrFail($id);

        //$files    = $folder->files()   ->orderBy('slug',     'asc')->paginate(5, ['*'], 'pageF');
        $posts    = $folder->posts()->orderBy('slug', 'asc')->paginate(5, ['*'], 'pageP');
        $profiles = $folder->profiles()->with('user')->orderBy('username', 'asc')->paginate(5, ['*'], 'pagePr');

        if ($folder) {
            if ($folder->status == 0) { $folder->path = private_path($folder->directory); }
            else                      { $folder->path =  public_path($folder->directory); }    
            return view('manage.folders.show', ['folder' => $folder, 'posts' => $posts, 'profiles' => $profiles, 'status_list' => $this->status()]);
        } else {
            Session::flash('failure', 'Folder "' . $id . '" was NOT found.');
            return Redirect::back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function getFile($id, $filename='Folder.jpg') {
        $myrc = false;
        $folder = Folder::findOrFail($id);
        if ($folder) {
            if ($folder->status == 0) { $path = private_path($folder->directory); }
            else                      { $path =  public_path($folder->directory); }   
            $path = $path . '\\' . $filename;
            if (File::exists($path)) {
                $file = File::get($path);
                $type = File::mimeType($path);
                $response = Response::make($file, 200);
                $response->header("Content-Type", $type);
                $myrc = true;
            }    
        }
        if ($myrc) { return $response; }
        else { abort(404); }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $folder = Folder::findOrFail($id);
        $categories = Category::orderBy('name', 'asc')->pluck('name', 'id');
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');

        if ($folder) {
            $folder->title = $folder->name;
            return view('manage.folders.edit', ['folder' => $folder,
                    'categories' => $categories, 'users' => $users, 'status_list' => $this->status()]);
        } else {
            Session::flash('failure', 'Folder "' . $id . '" was NOT found.');
            return Redirect::back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Folder $folder)
    {
        //
    }

    public function delete($id) {
        $folder = Folder::findOrFail($id);

        if ($folder) {
            
        } else {
            Session::flash('failure', 'Folder "' . $id . '" was NOT found.');
        }
        return view('manage.folders.delete', ['folder'=>$folder, 'status_list' => $this->status()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        $folder = Folder::findOrFail($id);

        if ($folder) {

        /*
        // If delete_photos was checked, delete all UNSHARED photos in this folder...
        // ...and issue warnings for all shared photos.  
        // If delete_photos was NOT checked and one or more photos would be left hanging...
        // without a parent folder, then return an error. 
        foreach ($folder->photos as $photo) {
            if ($request->delete_photos) {
                if ($photo->albums->count() == 1) { 
                    $photo->tags()->detach();
                    $photo->albums()->detach();
                    $myrc = $photo->delete();
                    if ($myrc) {
                        if ($photo->image) {
                            Storage::delete($photo->image);
                            Storage::delete($photo->file);
                            $msgx['info'][] = 'Image "' . $photo->image . '" was successfully deleted.';
                            $msgx['info'][] = 'Image "' . $photo->file  . '" was successfully deleted.';
                        }
                        $msgx['info'][] = 'Photo "' . $photo->title . '" was successfully deleted.';
                    } else {
                        $msgx['warning'][] = 'Photo "' . $id . '" was NOT found.';
                    }
                } else {
                    $msgx['warning'][] = 'Photo "' . $photo->title . '" is shared with another folder and will NOT be deleted!';
                }
            } elseif ($photo->albums->count() == 1) {
                $msgx['failure'][] = 'Folder "' . $folder->title .
                    '" cannot be deleted as it contains UNSHARED photos. You must choose to delete the photos along with the abum!';
                if (isset($msgx)) { session()->flash('msgx', $msgx); }
                return Redirect::back()->withInput();
            }          
        }    
        */

            // Now Delete the Folder
            $myrc = $folder->delete();
            if ($folder->status == 1) {
                $msg = 'Public';
                $path = public_path($folder->directory);
            } else {
                $msg = 'Private';
                $path = private_path($folder->directory);
            }
            $myrc = File::deleteDirectory($path);
            if ($myrc) {
                $msg = $msg . ' Directory "' . $folder->directory . '" was successfully deleted.';
                msgx(['Info:' => [$msg, true]]);
            } else {
                $msg = $msg . ' Directory "' . $folder->directory . '" could NOT be deleted.';
                msgx(['failure' => [$msg, true]]);                    
            }
            Session::flash('success', 'Folder "' . $folder->name . '" deleted OK.');
            return redirect()->route('albums.index');
        } else {
            Session::flash('failure', 'Folder "' . $id . '" was NOT found.');
            return Redirect::back()->withInput();
        }
    }

    /**
     * API Check unique
     * We check if exists & modify it if it does. 
     */
    public function apiCheckUnique(Request $request) {
        $slug = $request->slug;
    
        for ($i=1; $i<100; $i++) {
            if (!Folder::where('slug', '=', $slug)->exists()) {
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
        return json_encode($slug);
    }

}
