<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;
use Session;
use Validator;

class HomeController extends Controller
{
    
    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            $owner_id = $request->route('user') ?: '*';         
//dd(permit($this->permits($owner_id)));
            if (!permit($this->permits($owner_id))) {
                Session::flash('failure', "It doesn't look like you have permission for that action!");
                return redirect(previous());
            }

            session(['zone' => 'Blog']);                        // Set the active zone for search()
            previous(url($request->getPathInfo()));             // Set the previous url for redirect(previous()) 
            return $next($request);
        });
    }

    // These permits are used by permit() in the __contruct() middleware to secure the controller actions 
    // This could be done in the Route config - but it seems to make more sense to do it in the controller.
    // $owner_id='*' permits all Users for a permission   
    public function permits($owner_id='^') {
        $permits = [
            'changePassword' => 'permission:password-change',
            'updatePassword' => 'permission:password-change',
            'default'        => '' 
        ];
        return $permits;
    }

    public function changePassword(){
        return view('auth.changepassword');
    }

    // For validator confirmed to work...
    // if password field id=pw, then confirmed password field id=pw_confirmed 
    public function updatePassword(Request $request) {
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            Session::flash('failure', 'Current Password does not match.');
            return redirect()->route('changePassword')->withInput();
        }
        if(strcmp($request->get('current-password'), $request->get('new-password'))==0) {
            Session::flash('failure', 'New Password cannot be same as your Current Password.');
            return redirect()->route('changePassword')->withInput();
        }
        $validator = Validator::make($request->all(), [
            'current-password' => 'required',
            'new-password'     => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return redirect()->route('changePassword')->withErrors($validator)->withInput();
        }

        // Update Password here
        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        $user->save();
        Session::flash('success', 'New Password changed OK.');
        return redirect(previous());
        return redirect()->back();
    }

}
