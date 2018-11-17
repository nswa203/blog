<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Role;
use App\Permission;
use App\Team;
use App\User;
use Session;
use Validator;

class RoleController extends Controller
{
    
    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            session(['zone' => 'Roles']);
            return $next($request);
        });
    }

    // This Query Builder searches our table/columns and related_tables/columns for each word/phrase.
    // The table is sorted (ascending or descending) and finally filtered.
    // It requires the custom queryHelper() function in Helpers.php.
    public function searchSortQuery($request) {
        $query = [
            'model'         => 'Role',
            'searchModel'   => ['name', 'display_name', 'description'],
            'searchRelated' => [
                'users'       => ['name', 'email']
            ],
            'sortModel'   => [
                'i'       => 'd,id',                                                      
                'n'       => 'a,display_name',
                's'       => 'a,name',                                           
                'd'       => 'a,description',                                            
                'c'       => 'd,created_at',
                'u'       => 'd,updated_at',
                'default' => 'n'                       
            ]            
        ];
        return queryHelper($query, $request);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $pager = pageSize($request, 'rolesIndex', 12, 4, 192, 4);    // size($request->pp), sessionTag, default, min, max, step
        $roles = $this->searchSortQuery($request)->paginate($pager['size']);
        $roles->pager = $pager;

        if ($roles && $roles->count() > 0) {

        } else {
            Session::flash('failure', 'No Roles were found.');
        }
        return view('manage.roles.index', ['roles' => $roles, 'search' => $request->search, 'sort' => $request->sort]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $permissions = Permission::orderBy('display_name', 'asc')->paginate(999);
        return view('manage.roles.create', ['permissions' => $permissions]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // We explode the permissions into an array so that they may be handled by validate
        // & syncPermissions. Warning explode will create an empty element [0] if input is null.
        $permissions = $request->itemsSelected ? explode(',', $request->itemsSelected) : [];
        $request->merge(['permissions' => $permissions]);

        // We use our own validator here as auto validation cannot handle error return from a POST method 
        $validator = Validator::make($request->all(), [
            'display_name'  => 'required|min:3|max:191',
            'name'          => 'required|min:3|max:96|alpha_dash|unique:roles,name',            
            'description'   => 'sometimes|max:191',
            'permissions'   => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id'
        ]);
        if ($validator->fails()) {
            return redirect()->route('roles.create')->withErrors($validator)->withInput();
        }

        $role = new Role();
        $role->display_name = $request->display_name;
        $role->name         = $request->name; 
        $role->description  = $request->description; 
        $myrc = $role->save();

        if ($myrc) {
            $myrc = $role->syncPermissions($permissions);
        }

        if ($myrc) {
            Session::flash('success', 'Role "' . $role->display_name . '" was successfully saved.');
            return redirect()->route('roles.show', $role->id);
        } else {
            Session::flash('failure', 'Role "' . $request->display_name . '" was NOT saved.');
            return redirect()->route('roles.create')->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //$role = Role::where('id', $id)->with('permissions')->with('users')->first();
        $role = Role::find($id);

        if ($role) {
            $permissions = $role->permissions()->orderBy('display_name','asc')->paginate(5, ['*'], 'pagePm');
            $users       = $role->users()      ->orderBy('name',        'asc')->paginate(5, ['*'], 'pageU');

            return view('manage.roles.show', ['role' => $role, 'permissions' => $permissions, 'users' => $users]);
        } else {
            Session::flash('failure', 'Role "' . $id . '" was NOT found.');
            return redirect()->route('roles.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $role = Role::where('id', $id)->with('permissions')->first();

        if ($role) {
            $permissions = Permission::orderBy('display_name', 'asc')->paginate(999);
            return view('manage.roles.edit', ['role' => $role, 'permissions' => $permissions]);
        } else {
            Session::flash('failure', 'Role "' . $id . '" was NOT found.');
            return redirect()->route('roles.index');
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
        // We explode the permissions into an array so that they may be handled by validate
        // & syncPermissions. Warning explode will create an empty element [0] if input is null. 
        $permissions = $request->itemsSelected ? explode(',', $request->itemsSelected) : [];
        $request->merge(['permissions' => $permissions]);

        // We use our own validator here as auto validation cannot handle error return from a POST method 
        $validator = Validator::make($request->all(), [
            'display_name'  => 'required|min:3|max:191',
            'description'   => 'sometimes|max:191',
            'permissions'   => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id'
        ]);
        if ($validator->fails()) {
            return redirect()->route('roles.edit')->withErrors($validator)->withInput();
        }

        $role = Role::findOrFail($id);
        $role->display_name = $request->display_name; 
        $role->description  = $request->description; 
        $myrc = $role->save();

        if ($myrc) {
            $myrc = $role->syncPermissions($permissions);
        }

        if ($myrc) {
            Session::flash('success', 'Role "' . $role->display_name . '" was successfully saved.');
            return redirect()->route('roles.show', $role->id);
        } else {
            Session::flash('failure', 'Role "' . $id . '" was NOT saved.');
            return redirect()->route('roles.edit')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id) {
        $role = Role::find($id);

        if ($role) {
           return view('manage.roles.delete', ['role' => $role]);
        } else {
            Session::flash('failure', 'Role "' . $id . '" was NOT found.');
            return redirect()->route('roles.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $role = Role::findOrFail($id);

        $myrc = $role->delete();

        if ($myrc) {
            Session::flash('success', 'Role "' . $role->name . '" deleted OK.');
            return redirect()->route('roles.index');
        } else {
            Session::flash('failure', 'Role "' . $id . '" was NOT deleted.');
            return redirect()->route('roles.delete', $id)->withinput();
        }
    }

}
