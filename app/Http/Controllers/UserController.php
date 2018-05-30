<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Role;
use Session;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(10);

        if ($users) {

        } else {
            Session::flash('failure', 'No Users were found.');
        }
        return view('manage.users.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('manage.users.create');
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
            'name'  => 'required|min:3|max:191',
            'email' => 'required|email|max:191|unique:users',
        ]);

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
            Session::flash('success', 'The User was successfully saved.');
        } else {
            Session::flash('failure', 'The User was NOT saved.');
        }
        return redirect()->route('users.show', $user->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $roles = User::where('id', $id)->first()->roles()->orderBy('display_name','asc')->paginate(5);

        if ($user) {

        } else {
            Session::flash('failure', 'User "' . $id . '" was NOT found.');
        }
        return view('manage.users.show', ['user' => $user, 'roles' => $roles]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::where('id', $id)->with('roles')->first();
        $roles = Role::orderBy('display_name','asc')->paginate(10);

        if ($user) {

        } else {
            Session::flash('failure', 'User "' . $id . '" was NOT found.');
        }
        return view('manage.users.edit', ['user' => $user, 'roles' => $roles]);
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
        $this->validate($request, [
            'name'  => 'required|min:3|max:191',
            'email' => 'required|email|max:191|unique:users,email,'.$id
        ]);

        $user = User::findOrFail($id);
        $user->name     = $request->name;
        $user->email    = $request->email;

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

        if ($request->roles) {
            $user->syncRoles(explode(',', $request->roles));
        }

        if ($myrc) {
            Session::flash('success', 'The User was successfully saved.');
        } else {
            Session::flash('failure', 'The User was NOT saved.');
        }
        return redirect()->route('users.show', $user->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id) {
        $user = User::findOrFail($id);

        if ($user) {
            
        } else {
            Session::flash('failure', 'User "' . $id . '" was NOT found.');
        }
        return view('manage.users.delete', ['user' => $user]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->roles()->detach();
        $myrc = $user->delete();

        if ($myrc) {
            Session::flash('success', 'User successfully deleted.');
        } else {
            Session::flash('failure', 'The User was NOT deleted.');
        }
        return redirect()->route('users.index');
    }
}
