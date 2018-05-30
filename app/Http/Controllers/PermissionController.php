<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Permission;
use Session;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::orderBy('display_name', 'asc')->paginate(10);

        if ($permissions) {

        } else {
            Session::flash('failure', 'No Permissions were found.');
        }
        return view('manage.permissions.index', ['permissions' => $permissions]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('manage.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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
            $msgOK = 'The Permission for "'.$permission->display_name.'" was successfully saved.';
            $myrc = $permission->save();

        } elseif ($request->permission_type == 'crud') {
            $this->validate($request, $rules_crud);
            $crud = explode(',', $request->crud_selected);
            if (count($crud)>0) {
                foreach ($crud as $x) {
                    $resource = [
                        'display_name'  => ucwords($request->resource.' '.$x),
                        'name'          => strtolower($request->resource.'-'.$x),
                        'description'   => "Permits a User to ".strtoupper($x)." resource ".ucwords($request->resource)
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
                    if (!$myrc) { break; } else { $msgOK = $msgOK.', '.$permission->display_name; }
                }
            $msgOK = 'Permissions for "'.trim($msgOK,',').'" were sucessfully saved.';
            }    
        } else { $myrc = false; }

        if ($myrc) {
            Session::flash('success', $msgOK);
            return redirect()->route('permissions.index');
        } else {
            Session::flash('failure', 'The Permission was NOT saved.');
            return redirect()->route('permissions.create')->withInput();
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
        $permission = Permission::findOrFail($id);

        if ($permission) {

        } else {
            Session::flash('failure', 'Permission "' . $id . '" was NOT found.');
        }
        return view('manage.permissions.show', ['permission' => $permission]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);

        if ($permission) {

        } else {
            Session::flash('failure', 'Permission "' . $id . '" was NOT found.');
        }
        return view('manage.permissions.edit', ['permission' => $permission]);
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
            Session::flash('success', 'The Permission was successfully saved.');
        } else {
            Session::flash('failure', 'The Permission was NOT saved.');
        }
        return redirect()->route('permissions.show', $permission->id);
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
            
        } else {
            Session::flash('failure', 'Permission "' . $id . '" was NOT found.');
        }
        return view('manage.permissions.delete', ['permission' => $permission]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);

        if ($permission) {
            Session::flash('failure', 'Permission "' . $id . '" was NOT DELETED. DELETE Not yet supported!');
        } else {
            Session::flash('failure', 'Permission "' . $id . '" was NOT found.');
        }
        return view('manage.permissions.delete', ['permission' => $permission]);
    }
}
