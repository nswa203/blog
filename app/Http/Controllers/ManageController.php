<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManageController extends Controller
{

    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            $owner_id = $request->route('user') ?: '*';         

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
            'default' => 'user:*' 
        ];
        return $permits;
    }

	public function dashboard() {
    	return view('manage.dashboard');
    }	

}
