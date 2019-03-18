<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Profile;
use App\Folder;
use App\User;
use Auth;
use Image;
use Purifier;
use Session;
use Storage;
use Validator;

class ProfileController extends Controller
{

    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            $profile = $request->route('profile') ?: false;
            if ($profile) {
                $owner_id = Profile::find($profile)->user->id;
            } else {
                $owner_id = $request->route('user') ?: false;
            } 
            $owner_id = $owner_id ? $owner_id : '*'; 

            if (!permit($this->permits($owner_id))) {
                Session::flash('failure', "It doesn't look like you have permission for that action!");
                return redirect(previous());
            }

            // This codes extends validation to ensure restricted users may only access
            // their own User & Folder resources   
            if (permit($this->permits(), 'updateAll')) { 
                Validator::extend('ownFolder', function() { return true; });
                Validator::extend('ownUser'  , function() { return true; });
            } else {
                Validator::extend('ownFolder', function() use ($request) {
                    return areOwnedBy($request->folders, 'Folder', 'user_id', auth()->user()->id); },
                    "Invalid Folder."
                );
                Validator::extend('ownUser', function() use ($request) {
                    return $request->user_id==auth()->user()->id; },
                    "Invalid User."
                );
            } 

            session(['zone' => 'Profiles']);                    // Set the active zone for search()
            previous(url($request->getPathInfo()));             // Set the previous url for redirect(previous()) 
            return $next($request);
        });
    }

    // These permits are used by permit() in the __contruct() middleware to secure the controller actions 
    // This could be done in the Route config - but it seems to make more sense to do it in the controller.
    // $owner_id='*' permits all Users for a permission   
    public function permits($owner_id='^') {
        $permits = [
            'showAll'   => 'permission:profiles-read',
            'index'     => 'permission:profiles-read,owner:'.$owner_id.'|profiles-read-ifowner',
            'show'      => 'permission:profiles-read,owner:'.$owner_id.'|profiles-read-ifowner',
            'create'    => 'permission:profiles-create|profiles-create-ifowner',
            'store'     => 'permission:profiles-create|profiles-create-ifowner',
            'edit'      => 'permission:profiles-update,owner:'.$owner_id.'|profiles-update-ifowner',
            'updateAll' => 'permission:profiles-update',
            'update'    => 'permission:profiles-update,owner:'.$owner_id.'|profiles-update-ifowner',
            'delete'    => 'permission:profiles-delete,owner:'.$owner_id.'|profiles-delete-ifowner',
            'destroy'   => 'permission:profiles-delete,owner:'.$owner_id.'|profiles-delete-ifowner',
            'default'   => '' 
        ];
        return $permits;
    }

    // This Query Builder searches our table/columns and related_tables/columns for each word/phrase.
    // The table is sorted (ascending or descending) and finally filtered.
    // It requires the custom queryHelper() function in Helpers.php.
    public function query($user_id=false) {
        $query = [
            'model'         => 'Profile',
            'searchModel'   => ['username', 'about_me', 'phone', 'address'],
            'searchRelated' => [
                'folders' => ['name', 'slug', 'directory', 'description'],
                'user'    => ['name', 'email']
            ],
            'sort'        => [
                'i'       => 'd,id',                                                      
                'p'       => 'a,username',
                'c'       => 'd,created_at',
                'u'       => 'd,updated_at',
                'n'       => 'a,name,user',
                'e'       => 'a,email,user',
                'default' => 'n'                       
            ]                        
        ];        
        if ($user_id) {
            $query['filter'] = ['user_id', '=', $user_id];
        } 
        return $query;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $id = permit($this->permits(), 'showAll') ? '' : Auth::user()->id;
        $profiles = paginateHelper($this->query($id), $request, 12, 4, 192, 4);    

        if ($profiles && $profiles->count()>0) {

        } else {
            Session::flash('failure', 'No Profiles were found.');
        }
        return view('manage.profiles.index', ['profiles' => $profiles, 'search' => $request->search, 'sort' => $request->sort]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id) {
        $profile = new Profile;

        if (permit($this->permits(), 'updateAll')) {
            $folders = Folder::orderBy('name', 'asc')->pluck('name', 'id');
            $users   =   User::orderBy('name', 'asc')->pluck('name', 'id');            
        } else {
            $folders = Folder::where('user_id', Auth::user()->id)->orderBy('name', 'asc')->pluck('name', 'id');
            $users   = [Auth::user()->id => Auth::user()->name];            
        }

        return view('manage.profiles.create', ['profile' => $profile, 'folders' => $folders, 'users' => $users, 'id' => $id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // We use our own validator here as auto validation cannot handle error return from a POST method
        $validator = Validator::make($request->all(), [
            'username'  => 'required|min:3|max:191|unique:profiles,username',
            'image'     => 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
            'banner'    => 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
            'about_me'  => 'sometimes|max:2048',
            'phone'     => 'sometimes|max:191',
            'address'   => 'sometimes|max:191',
            'user_id'   => 'sometimes|integer|exists:users,id|unique:profiles,user_id|ownUser',
            'folders'   => 'array|ownFolder',
            'folders.*' => 'integer|exists:folders,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('profiles.create', ['profile' => '0'])->withErrors($validator)->withInput();
        }        

        $profile = new Profile;
        $profile->username         = $request->username;
        $profile->phone            = $request->phone;
        $profile->address          = Purifier::clean($request->address);
        $profile->about_me         = Purifier::clean($request->about_me);
        $profile->user_id          = $request->user_id ? $request->user_id : auth()->user()->id;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = microtime() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images\\' . $filename);
            Image::make($image)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
            $profile->image = $filename;
            $msgs[] = 'Image "' . $image->getClientOriginalName() . '" was successfully saved as ' . $filename;
        }
        if ($request->hasFile('banner')) {
            $image = $request->file('banner');
            $filename = microtime() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images\\' . $filename);
            Image::make($image)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
            $profile->banner = $filename;
            $msgs[] = 'Image "' . $image->getClientOriginalName() . '" was successfully saved as ' . $filename;
        }

        $myrc = $profile->save();

        if (isset($msgs)) { session()->flash('msgs', $msgs); }

        if ($myrc) {
            $myrc = $profile->folders()->sync($request->folders, false);
            Session::flash('success', 'Profile "' . $profile->username . '" was successfully saved.');
            return redirect()->route('profiles.show', $profile->id);
        } else {
            Session::flash('failure', 'Profile "' . $profile->username . '" was NOT saved.');
            return redirect()->route('profiles.create', $id)->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $profile = Profile::with('user')->find($id);

        if ($profile) {
            $user = User::find($profile->user->id);
            $folder_list = $user->folders()->get()->pluck('id')->merge($profile->folders()->get()->pluck('id'))->unique();
            $folders = Folder::whereIn('id', $folder_list)->orderBy('slug', 'asc')->paginate(5, ['*'], 'pageF');
            return view('manage.profiles.show', ['profile' => $profile, 'folders' => $folders]);
        } else {
            Session::flash('failure', 'Profile "' . $id . '" was NOT found.');
            return redirect(previous());
            return redirect()->route('profiles.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $profile = Profile::with('user')->find($id);

        if ($profile) {
            if (permit($this->permits(), 'updateAll')) {
                $folders = Folder::orderBy('name', 'asc')->pluck('name', 'id');
            } else {
                $folders = Folder::where('user_id', Auth::user()->id)->orderBy('name', 'asc')->pluck('name', 'id');
            }
        
            $users   = [$profile->user->id => $profile->user->name];            
            return view('manage.profiles.edit', ['profile' => $profile, 'folders' => $folders, 'users' => $users]);
        } else {
            Session::flash('failure', 'Profile "' . $id . '" was NOT found.');
            return redirect(previous());
            return redirect()->route('profiles.index');
        }
    }        

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $profile = Profile::findOrFail($id);

        // We use our own validator here as auto validation cannot handle error return from a POST method 
        $validator = Validator::make($request->all(), [
            'username'          => 'required|min:3|max:191|unique:profiles,username,' . $id,
            'image'             => 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
            'banner'            => 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
            'about_me'          => 'sometimes|max:2048',
            'phone'             => 'sometimes|max:191',
            'address'           => 'sometimes|max:191',
            'folders'           => 'array|ownFolder',
            'folders.*'         => 'integer|exists:folders,id',              
        ]);
        if ($validator->fails()) {
            return redirect()->route('profiles.edit', $id)->withErrors($validator)->withInput();
        }        

        $profile->username         = $request->username;
        $profile->phone            = $request->phone;
        $profile->address          = Purifier::clean($request->address);
        $profile->about_me         = Purifier::clean($request->about_me);

        if ($request->hasFile('image')) {
            $oldFiles[]=$profile->image;
            $image = $request->file('image');
            $filename = microtime() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images\\' . $filename);
            Image::make($image)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
            $profile->image = $filename;
            $msgs[] = 'Image "' . $image->getClientOriginalName() . '" was successfully saved as ' . $filename;
        } elseif ($request->delete_image) {
            $oldFiles[] = $profile->image;
            $msgs[] = 'Image "' . $profile->image . '" deleted.';
            $profile->image = null;
        } else {
            //$msgs[] = 'Image "' . $profile->image . '" was successfully saved.';
        }

        if ($request->hasFile('banner')) {
            $oldFiles[] = $profile->banner;
            $banner = $request->file('banner');
            $filename = microtime() . '.' . $banner->getClientOriginalExtension();
            $location = public_path('images\\' . $filename);
            Image::make($banner)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
            $profile->banner = $filename;
            $msgs[] = 'Image "' . $banner->getClientOriginalName() . '" was successfully saved as ' . $filename;
        } elseif ($request->delete_banner) {
            $oldFiles[] = $profile->banner;
            $msgs[] = 'Image "' . $profile->banner . '" deleted.';
            $profile->banner = null;
        } else {
            //$msgs[] = 'Image "' . $profile->banner . '" was successfully saved.';
        }

        $myrc = $profile->save();

        if (isset($msgs)) { session()->flash('msgs', $msgs); }

        if ($myrc) {
            $myrc = $profile->folders()->sync($request->folders, true);
            if (isset($oldFiles)) { Storage::delete($oldFiles); }
            Session::flash('success', 'Profile "' . $profile->username . '" was successfully saved.');
            return redirect()->route('profiles.show', $id);
        } else {
            Session::flash('failure', 'Profile "' . $id . '" was NOT saved.');
            return redirect()->route('profiles.edit', $id)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id) {
        $profile = Profile::find($id);

        if ($profile) {
            return view('manage.profiles.delete', ['profile' => $profile]);
        } else {
            Session::flash('failure', 'You do NOT have permission to delete this Profile.');
            return redirect(previous());
            return redirect()->route('profiles.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $profile = Profile::findOrFail($id);

        $myrc = $profile->delete();

        if ($myrc) {
            if ($profile->image) {
                Storage::delete($profile->image);
                $msgs[] = 'Image "' . $profile->image . '" was successfully deleted.';
            }
            if ($profile->banner) {
                Storage::delete($profile->banner);
                $msgs[] = 'Image "' . $profile->banner . '" was successfully deleted.';
            }
            Session::flash('success', 'Profile ' . $profile->username . ' was successfully deleted.');
            if (isset($msgs)) { session()->flash('msgs', $msgs); }
            return redirect()->route('profiles.index');
        } else {
            Session::flash('failure', 'Profile ' . $id . ' was NOT deleted.');
            return redirect()->route('profiles.delete', $id)->withinput();
        }
    }

}
