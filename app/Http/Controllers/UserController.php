<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Pagination\Paginator;
use App\User;
use App\Folder;
use App\Permission;
use App\Profile;
use App\Role;
use Auth;
use File;
use Session;
use Validator;

class UserController extends Controller
{

    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) { 
            $owner_id = $request->route('user') ?: '*';         // This gets {user} from Route:: statement (if {user} was coded)
            if (! permit($this->permits($owner_id))) {          // Check Permission for this controller action
                Session::flash('failure', "It doesn't look like you have permission for that action!");
                return redirect(previous());
            }

            session(['zone' => 'Users']);                       // Set the active zone for search()
            previous(url($request->getPathInfo()));             // Set the previous url for redirect(previous()) 
            return $next($request);
        });
    }

    // These permits are used by permit() in the __contruct() middleware to secure the controller actions 
    // This could be done in the Route config - but it seems to make more sense to do it in the controller.
    // $owner_id='*' permits all Users for a permission but ... only if any additional permission is set  
    public function permits($owner_id='^') {
        $permits = [
            'showAll' => 'permission:users-read',
            'index'   => 'permission:users-read,owner:'.$owner_id.'|users-read-ifowner',
            'show'    => 'permission:users-read,owner:'.$owner_id.'|users-read-ifowner',
            'create'  => 'permission:users-create',
            'store'   => 'permission:users-create',
            'edit'    => 'permission:users-update,owner:'.$owner_id.'|users-update-ifowner',
            'update'  => 'permission:users-update,owner:'.$owner_id.'|users-update-ifowner',
            'delete'  => 'permission:users-delete,owner:'.$owner_id.'|users-delete-ifowner',
            'destroy' => 'permission:users-delete,owner:'.$owner_id.'|users-delete-ifowner',
            'default' => '' 
        ];
        return $permits;
    }

    // This Query Builder searches our table/columns and related_tables/columns for each word/phrase.
    // The table is sorted (ascending or descending) and finally filtered.
    // It requires the custom queryHelper() function in Helpers.php.
    public function query($user_id=false) {
        $query = [
            'model'         => 'User',
            'searchModel'   => ['name', 'email'],
            'searchRelated' => [
                'folders'     => ['name', 'slug', 'directory', 'description'],
                'permissions' => ['name', 'display_name', 'description'],
                'profile'     => ['username', 'about_me', 'phone', 'address'],
                'roles'       => ['name', 'display_name', 'description']
            ],
            'sort'        => [
                'i'       => 'd,id',                                                      
                'n'       => 'a,name',
                'e'       => 'a,email',
                'c'       => 'd,created_at',
                'u'       => 'd,updated_at',
                'p'       => 'a,username,profile',
                'default' => 'n'                       
            ],                   
        ];
        if ($user_id) {
            $query['filter'] = ['id', '=', $user_id];
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
        $users = paginateHelper($this->query($id), $request, 12, 4, 192, 4); 

        if ($users && $users->count()>0) {

        } else {
            Session::flash('failure', 'No Users were found.');
        }
        return view('manage.users.index', ['users' => $users, 'search' => $request->search, 'sort' => $request->sort]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $roles = Role::orderBy('display_name', 'asc')->paginate(999);
        return view('manage.users.create', ['roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // We explode the roles into an array so that they may be handled by validate
        // & syncRoles. Warning explode will create an empty element [0] if input is null. 
        $roles = $request->itemsSelected ? explode(',', $request->itemsSelected) : [];
        $request->merge(['roles' => $roles]);
        $msg = '';
 
        $validator = Validator::make($request->all(), [
            'name'      => 'required|min:3|max:191',
            'email'     => 'required|min:5|max:191|email|unique:users',
            'password'  => 'sometimes|min:7|max:96',
            'roles'     => 'sometimes|array',
            'roles.*'   => 'exists:roles,id'
        ]);
        if ($validator->fails()) {
            return redirect()->route('users.create')->withErrors($validator)->withInput();
        }

        if ($request->has('password') && !empty($request->password)) {
            $password = trim($request->password);
        } else {
            $length     = 10;
            $keyspace   = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
            $str        = '';
            $max        = mb_strlen($keyspace, '8bit')-1;
            for ($i=0; $i<$length; ++$i) {
                $str .= $keyspace[random_int(0, $max)];
            } 
            $password = $str;
        }

        $user = new User;
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->password = Hash::make($password);
        $myrc = $user->save();

        if ($myrc) {
            $myrc = $user->syncRoles($roles);

            // php artisan make:notification CustomResetPassword
            // edit CustomResetPassword.php
            // edit CanRestPassword.php
            // edit PasswordBroker.php
            $credentials = ['email' => $user->email]; 
            $response = Password::sendResetLink($credentials, $user);        
            if (Password::RESET_LINK_SENT) {
                $msg = ' and an eMail notification was sent to ' . $user->email;
            } else {
                $msg = ' but an eMail notification could NOT be sent to ' . $user->email;
            }   
        }

        if ($myrc) {
            Session::flash('success', 'User "' . $user->name . '" was successfully saved' . $msg);
            return redirect()->route('users.show', $user->id);
        } else {
            Session::flash('failure', 'User "' . $request->name . '" was NOT saved.');
            return redirect()->route('users.create')->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $user = User::find($id);

        if ($user) {
            $list['a'] = albumStatus();
            $list['d'] = folderStatus();
            $list['p'] = postStatus();
            
            $albums = $user->albums()->orderBy('slug', 'asc')->paginate(5, ['*'], 'pageA');

            if($user->profile) { 
                $profile = Profile::find($user->profile->id);
                $folder_list = $user->folders()->get()->pluck('id')->merge($profile->folders()->get()->pluck('id'))->unique();
                $folders = Folder::whereIn('id', $folder_list)->orderBy('slug', 'asc')->paginate(5, ['*'], 'pageF');
                $folders_total = $folder_list->count();
            } else {
                $folders = $user->folders()->orderBy('slug', 'asc')->paginate(5, ['*'], 'pageF');
                $folders_total = $folders->count();
            }

            $posts = $user->posts()->orderBy('slug', 'asc')->paginate(5, ['*'], 'pageP');
            $roles = $user->roles()->orderBy('display_name', 'asc')->paginate(5, ['*'], 'pageR');
            $permissions = Permission::whereIn('id', $user->allPermissions()->pluck('id'))->orderBy('display_name', 'asc')
                ->paginate(5, ['*'], 'pagePm');

            return view('manage.users.show', [
                'user'  => $user,    'albums' => $albums,   'folders'     => $folders,      'folders_total' => $folders_total,
                'posts' => $posts,   'roles'  => $roles,    'permissions' => $permissions,  'list'          => $list]);
        } else {
            Session::flash('failure', 'User "' . $id . '" was NOT found.');
            return redirect(previous());
            return redirect()->route('users.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $user = User::where('id', $id)->with('roles')->first();
        $roles = Role::orderBy('display_name','asc')->paginate(999);

        if ($user) {
            return view('manage.users.edit', ['user' => $user, 'roles' => $roles]);
        } else {
            Session::flash('failure', 'User "' . $id . '" was NOT found.');
            return redirect(previous());
            return redirect()->route('users.index');
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
        // We explode the roles into an array so that they may be handled by validate
        // & syncRoles. Warning explode will create an empty element [0] if input is null. 
        $roles = $request->itemsSelected ? explode(',', $request->itemsSelected) : [];
        $request->merge(['roles' => $roles]);
        $msg = ''; 

        $validator = Validator::make($request->all(), [
            'name'      => 'required|min:3|max:191',
            //'email'     => 'required|min:5|max:191|email|unique:users,email,' . $id,
            'password'  => 'sometimes|min:7|max:96',
            'roles'     => 'sometimes|array',
            'roles.*'   => 'exists:roles,id'
        ]);
        if ($validator->fails()) {
            return redirect()->route('users.edit', [$id])->withErrors($validator)->withInput();
        }

        $user = User::findOrFail($id);
        $user->name     = $request->name;
        //$user->email    = $request->email;

        if ($request->password_option == 'auto') {
            $length     = 10;
            $keyspace   = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
            $str        = '';
            $max        = mb_strlen($keyspace, '8bit')-1;
            for ($i=0; $i<$length; ++$i) {
                $str .= $keyspace[random_int(0, $max)];
            } 
            $user->password = Hash::make($str);
        } elseif ($request->password_option == 'manual') {
            $user->password = Hash::make($request->password);
        }

        $myrc = $user->save();

        if ($myrc) {
            $myrc = $user->syncRoles($roles);

            if ($request->password_option == 'auto' or $request->password_option == 'manual') {
                // php artisan make:notification CustomResetPassword
                // edit CustomResetPassword.php
                // edit CanRestPassword.php
                // edit PasswordBroker.php
                $credentials = ['email' => $user->email]; 
                $response = Password::sendResetLink($credentials, $user);        
                if (Password::RESET_LINK_SENT) {
                    $msg = ' and an eMail notification was sent to ' . $user->email;
                } else {
                    $msg = ' but an eMail notification could NOT be sent to ' . $user->email;
                }
            }       
        }

        if ($myrc) {
            Session::flash('success', 'User "' . $user->name . '" was successfully saved' . $msg);
            return redirect()->route('users.show', $user->id);
        } else {
            Session::flash('failure', 'User "' . $id . '" was NOT saved.');
            return redirect()->route('users.edit', $id)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id) {
        $user = User::find($id);

        if ($user) {
            if (isset($user->profile->username)) {
                $msg = 'User Profile "' . $user->profile->username . '" will also be deleted!';
                msgx(['Dependencies' => [$msg, true]]);
            }
            foreach ($user->albums as $album) {
                $msg = 'Album "' . $album->title . '" will also be deleted!';
                msgx(['Dependencies' => [$msg, true]]);                    
            }
            foreach ($user->folders as $folder) {
                $msg = 'Folder "' . $folder->name . '" will also be deleted!';
                msgx(['Dependencies' => [$msg, true]]);
            }
            foreach ($user->posts as $post) {
                $msg = 'Post "' . $post->title . '" will also be deleted!';
                msgx(['Dependencies' => [$msg, true]]);                    
            }
            return view('manage.users.delete', ['user' => $user]);
        } else {
            Session::flash('failure', 'User "' . $id . '" was NOT found.');
            return redirect(previous());
            return redirect()->route('users.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $user = User::findOrFail($id);

        if (isset($user->profile->username)) {
            if ($user->profile->banner) {
                $path = public_path('images\\' . $user->profile->banner);
                $myrc = File::delete($path);
                $msg = 'Profile Banner "' . $path . '" was successfully deleted.';
                msgx(['Dependencies' => [$msg, $myrc]]);
            }
            if ($user->profile->image) {
                $path = public_path('images\\' . $user->profile->image);
                $myrc = File::delete($path);
                $msg = 'Profile Image "' . $path . '" was successfully deleted.';
                msgx(['Dependencies' => [$msg, $myrc]]);
            }                
        }
        foreach ($user->albums as $album) {
            $myrc = true;
            $msg = 'Album "' . $album->title . '" was successfully deleted';
            msgx(['Dependencies' => [$msg, $myrc!=null]]);                    
        }
        foreach ($user->folders as $folder) {
            if ($folder->status == 1) {
                $msg = 'Public';
                $path = public_path($folder->directory);
            } else {
                $msg = 'Private';
                $path = private_path($folder->directory);
                $userPath = dirname($path);
            }
            $myrc = File::deleteDirectory($path);
            if ($myrc) {
                $msg = $msg . ' Directory "' . $folder->directory . '" was successfully deleted.';
                msgx(['Dependencies' => [$msg, true]]);
            } else {
                $msg = $msg . ' Directory "' . $folder->directory . '" could NOT be deleted.';
                msgx(['failure' => [$msg, true]]);                    
            }    
        }
        foreach ($user->posts as $post) {
            if ($post->banner) {
                $path = public_path('images\\' . $post->banner);
                $myrc = File::delete($path);
                $msg = 'Post Banner "' . $path . '" was successfully deleted.';
                msgx(['Dependencies' => [$msg, $myrc]]);
            }
            if ($post->image) {
                $path = public_path('images\\' . $post->image);
                $myrc = File::delete($path);
                $msg = 'Post Image "' . $path . '" was successfully deleted.';
                msgx(['Dependencies' => [$msg, $myrc]]);
            }      
        }
        if (isset($userPath)) { File::deleteDirectory($userPath); }

        $myrc = $user->delete(); // Migration Cascades will handle all DB dependencies  

        if ($myrc) {
            Session::flash('success', 'User "' . $user->name . '" was successfully deleted.');
            return redirect()->route('users.index');
        } else {
            Session::flash('failure', 'User "' . $id . '" was NOT deleted.');
            return redirect()->route('users.delete', $id)->withinput();
        }
    }

}
