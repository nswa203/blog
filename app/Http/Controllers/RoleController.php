<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\Permission;
use Session;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::orderBy('display_name', 'asc')->paginate(10);

        if ($roles) {

        } else {
            Session::flash('failure', 'No Roles were found.');
        }
        return view('manage.roles.index', ['roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::orderBy('display_name', 'asc')->paginate(10);
        return view('manage.roles.create', ['permissions' => $permissions]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // We explode the permissions into an array so that they may be handled by validate
        // & syncPermissions. Warning explode will create an empty element [0] if input is null. 
        $permissions = $request->permissions ? explode(',', $request->permissions) : [];
        $request->merge(['permissions' => $permissions]);
    
        $this->validate($request, [
            'display_name'  => 'required|min:3|max:191',
            'name'          => 'required|min:3|max:96|alpha_dash|unique:roles,name',            
            'description'   => 'sometimes|max:191',
            'permissions'   => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id'
        ]);  

        $role = new Role();
        $role->display_name = $request->display_name;
        $role->name         = $request->name; 
        $role->description  = $request->description; 
        $myrc = $role->save();

        if ($myrc) {
            $myrc = $role->syncPermissions($permissions);
        }

        if ($myrc) {
            Session::flash('success', 'The Role was successfully saved.');
            return redirect()->route('roles.show', $role->id);
        } else {
            Session::flash('failure', 'The Role was NOT saved.');
            return redirect()->route('manage.roles.create')->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::where('id', $id)->with('permissions')->with('users')->first();
        $permissions = $role->permissions()->orderBy('display_name','asc')->paginate(10);
        $users = $role->users()->orderBy('name','asc')->paginate(10);

        if ($role) {

        } else {
            Session::flash('failure', 'Role "' . $id . '" was NOT found.');
        }
        return view('manage.roles.show', ['role' => $role, 'permissions' => $permissions, 'users' => $users]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::where('id', $id)->with('permissions')->first();
        $permissions = Permission::orderBy('display_name', 'asc')->paginate(10);

        if ($role) {

        } else {
            Session::flash('failure', 'Role "' . $id . '" was NOT found.');
        }
        return view('manage.roles.edit', ['role' => $role, 'permissions' => $permissions]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // We explode the permissions into an array so that they may be handled by validate
        // & syncPermissions. Warning explode will create an empty element [0] if input is null. 
        $permissions = $request->permissions ? explode(',', $request->permissions) : [];
        $request->merge(['permissions' => $permissions]);
    
        $this->validate($request, [
            'display_name'  => 'required|min:3|max:191',
            'description'   => 'sometimes|max:191',
            'permissions'   => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id'
        ]);  

        $role = Role::findOrFail($id);
        $role->display_name = $request->display_name; 
        $role->description  = $request->description; 
        $myrc = $role->save();

        if ($myrc) {
            $myrc = $role->syncPermissions($permissions);
        }

        if ($myrc) {
            Session::flash('success', 'The Role was successfully saved.');
            return redirect()->route('roles.show', $id);
        } else {
            Session::flash('failure', 'The Role was NOT saved.');
            return redirect()->route('roles.create')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id) 
    {
        $role = Role::findOrFail($id);

        if ($role) {
            
        } else {
            Session::flash('failure', 'Role "' . $id . '" was NOT found.');
        }
        return view('manage.roles.delete', ['role' => $role]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role) {
            Session::flash('failure', 'Role "' . $id . '" was NOT DELETED. DELETE Not yet supported!');
        } else {
            Session::flash('failure', 'Role "' . $id . '" was NOT found.');
        }
        return view('manage.roles.delete', ['role' => $role]);
    }
}
