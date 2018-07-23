<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Permission;
use App\Role;
use App\User;
use Session;

class PermissionController extends Controller
{

    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            session(['zone' => 'Permissions']);
            return $next($request);
        });
    }

    // This Query Builder searches our table/columns and related_tables/columns for each word/phrase.
    // It requires the custom search_helper() function in Helpers.php.
    // If you change Helpers.php you should do "dump-autoload". 
    public function searchQuery($search = '') {
        $query = [
            'model'         => 'Permission',
            'searchModel'   => ['name', 'display_name', 'description'],
            'searchRelated' => [
                'roles' => ['name', 'display_name', 'description'],
                'users' => ['name', 'email']
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
        $permissions = $this->searchQuery($request->search)->orderBy('display_name', 'asc')->paginate(10);
        if ($permissions) {

        } else {
            Session::flash('failure', 'No Permissions were found.');
        }
        return view('manage.permissions.index', ['permissions' => $permissions, 'search' => $request->search]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('manage.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $myrc           = false;
        $msgOK          = '';
        $rules_basic    = [
            'display_name'  => 'required|min:3|max:191',
            'name'          => 'required|min:3|max:191|alphadash|unique:permissions,name',
            'description'   => 'sometimes|max:191',
        ];
        $rules_crud     = [
            'resource'      => 'required|min:3|max:191|alpha',
            'crud_selected' => 'required|min:4|max:25',
        ];

        if ($request->permission_type == 'basic') {
            $this->validate($request, $rules_basic);
            $permission = new Permission();
            $permission->name           = $request->name;
            $permission->display_name   = $request->display_name;
            $permission->description    = $request->description;
            $msgOK = 'Permission "' . $permission->display_name . '" was successfully saved.';
            $myrc = $permission->save();

        } elseif ($request->permission_type == 'crud') {
            $this->validate($request, $rules_crud);
            $crud = explode(',', $request->crud_selected);
            if (count($crud)>0) {
                foreach ($crud as $x) {
                    $resource = [
                        'display_name'  => ucwords($request->resource . ' ' . $x),
                        'name'          => strtolower($request->resource . '-' . $x),
                        'description'   => "Permits a User to " . strtoupper($x) . " resource " . ucwords($request->resource)
                    ];
                    $validator = Validator::make($resource, $rules_basic);
                    if($validator->fails()) {
                        return redirect()->route('permissions.create')->withInput()->withErrors($validator);
                    } else { 
                        $permission = new Permission();
                        $permission->display_name   = $resource['display_name'];
                        $permission->name           = $resource['name'];
                        $permission->description    = $resource['description'];
                        $myrc = $permission->save();
                    }      
                    if (!$myrc) { break; } else { $msgOK = $msgOK . ', ' . $permission->display_name; }
                }
            $msgOK = 'Permissions for "' . trim($msgOK,',') . '" were sucessfully saved.';
            }    
        } else { $myrc = false; }

        if ($myrc) {
            Session::flash('success', $msgOK);
            return redirect()->route('permissions.show', $permission->id);
        } else {
            Session::flash('failure', 'Permission "' . $request->display_name .'" was NOT saved.');
            return redirect()->route('manage.permissions.create')->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $permission = Permission::findOrFail($id);

        $roles = $permission->roles()->with('users')->get();
        $users = [];
        foreach ($roles as $role) {
            foreach ($role->users as $user) {
                if (!in_array($user->id, $users)) {
                    $users[] = $user->id;
                }    
            }    
        }
        $roles = $permission->roles()       ->orderBy('display_name', 'asc')->paginate(5, ['*'], 'pageR');
        $users = User::whereIn('id', $users)->orderBy('name',         'asc')->paginate(5, ['*'], 'pageU');

        if ($permission) {
            return view('manage.permissions.show', ['permission' => $permission, 'roles' => $roles, 'users' => $users]);
        } else {
            Session::flash('failure', 'Permission "' . $id . '" was NOT found.');
            return Redirect::back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $permission = Permission::findOrFail($id);

        if ($permission) {
            return view('manage.permissions.edit', ['permission' => $permission]);
        } else {
            Session::flash('failure', 'Permission "' . $id . '" was NOT found.');
            return Redirect::back();
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
        $rules_basic = [
            'display_name'  => 'required|min:3|max:191',
            'description'   => 'sometimes|max:191',
        ];
        $this->validate($request, $rules_basic);

        $permission = Permission::findOrFail($id);
        $permission->display_name   = $request->display_name;
        $permission->description    = $request->description;
        $myrc = $permission->save();

        if ($myrc) {
            Session::flash('success', 'Permission "' . $permission->display_name . '" was successfully saved.');
            return redirect()->route('permissions.show', $permission->id);
        } else {
            Session::flash('failure', 'Permission "' . $id . '" was NOT saved.');
            return redirect()->route('manage.permissions.edit')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id) {
        $permission = Permission::findOrFail($id);

        if ($permission) {
            return view('manage.permissions.delete', ['permission' => $permission]);
        } else {
            Session::flash('failure', 'Permission "' . $id . '" was NOT found.');
            return Redirect::back();            
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $permission = Permission::findOrFail($id);

        if ($permission) {
            $myrc = $permission->delete();
            Session::flash('success', 'Permission "' . $permission->name . '" deleted OK.');
            return redirect()->route('permissions.index');
        } else {
            Session::flash('failure', 'Permission "' . $id . '" was NOT deleted.');
            return Redirect::back();            
        }
    }

}
