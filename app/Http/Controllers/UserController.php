<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\User;
use App\Role;
use Session;

use Illuminate\Support\Facades\Password;

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
        $roles = Role::orderBy('display_name', 'asc')->paginate(999);
        return view('manage.users.create', ['roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // We explode the roles into an array so that they may be handled by validate
        // & syncRoles. Warning explode will create an empty element [0] if input is null. 
        $roles = $request->itemsSelected ? explode(',', $request->itemsSelected) : [];
        $request->merge(['roles' => $roles]);
        $msg = '';
   
        $this->validate($request, [
            'name'      => 'required|min:3|max:191',
            'email'     => 'required|min:5|max:191|email|unique:users',
            'password'  => 'sometimes|min:7|max:96',
            'roles'     => 'sometimes|array',
            'roles.*'   => 'exists:roles,id'            
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
            $myrc = $user->syncRoles($roles);
      
            // php artisan make:notification CustomResetPassword
            // edit CustomResetPassword.php
            // edit CanRestPassword.php
            // edit PasswordBroker.php
            $credentials = ['email' => $user->email]; 
            $response = Password::sendResetLink($credentials, $user);        
            if (Password::RESET_LINK_SENT) {
                $msg = ' and eMail notification was sent to ' . $user->email;
            } else {
                $msg = ' but eMail notification could NOT be sent to ' . $user->email;
            }   

        }

        if ($myrc) {
            Session::flash('success', 'User "' . $user->name . '" was successfully saved' . $msg);
            return redirect()->route('users.show', $user->id);
        } else {
            Session::flash('failure', 'User "' . $request->name . '" was NOT saved.');
            return redirect()->route('manage.users.create')->withInput();
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
        $user = User::findOrFail($id);
        $roles = User::where('id', $id)->first()->roles()->orderBy('display_name', 'asc')->paginate(5);

        if ($user) {
            return view('manage.users.show', ['user' => $user, 'roles' => $roles]);
        } else {
            Session::flash('failure', 'User "' . $id . '" was NOT found.');
            return Redirect::back();
        }
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
        $roles = Role::orderBy('display_name','asc')->paginate(999);

        if ($user) {
            return view('manage.users.edit', ['user' => $user, 'roles' => $roles]);
        } else {
            Session::flash('failure', 'User "' . $id . '" was NOT found.');
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
    public function update(Request $request, $id)
    {
        // We explode the roles into an array so that they may be handled by validate
        // & syncRoles. Warning explode will create an empty element [0] if input is null. 
        $roles = $request->itemsSelected ? explode(',', $request->itemsSelected) : [];
        $request->merge(['roles' => $roles]);
   
        $this->validate($request, [
            'name'      => 'required|min:3|max:191',
            'email'     => 'required|min:5|max:191|email|unique:users,email,' . $id,
            'password'  => 'sometimes|min:7|max:96',
            'roles'     => 'sometimes|array',
            'roles.*'   => 'exists:roles,id'            
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

        if ($myrc) {
            $myrc = $user->syncRoles($roles);
        }

        if ($myrc) {
            Session::flash('success', 'User "' . $user->name . '" was successfully saved.');
            return redirect()->route('users.show', $user->id);
        } else {
            Session::flash('failure', 'User "' . $id . '" was NOT saved.');
            return redirect()->route('manage.users.edit', $id)->withInput();
        }
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
            return view('manage.users.delete', ['user' => $user]);
        } else {
            Session::flash('failure', 'User "' . $id . '" was NOT found.');
            return Redirect::back();            
        }
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
            Session::flash('success', 'User "' . $user->name . '" was successfully deleted.');
            return redirect()->route('users.index');
        } else {
            Session::flash('failure', 'User "' . $id . '" was NOT deleted.');
            return Redirect::back();            
        }
    }
}
