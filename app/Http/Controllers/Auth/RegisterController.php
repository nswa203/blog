<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Profile;
use Session;

class RegisterController extends Controller {
	/*
		    |--------------------------------------------------------------------------
		    | Register Controller
		    |--------------------------------------------------------------------------
		    |
		    | This controller handles the registration of new users as well as their
		    | validation and creation. By default this controller uses a trait to
		    | provide this functionality without requiring any additional code.
		    |
	*/

	use RegistersUsers;

	/**
	 * Where to redirect users after registration.
	 *
	 * @var string
	 */
	protected $redirectTo = '/home';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->middleware('guest');
	}

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validator(array $data) {
		return Validator::make($data, [
			'name'     => 'required|string|max:191',
			'email'    => 'required|string|email|max:191|unique:users',
            'username' => 'required|string|min:3|max:191|unique:profiles,username',
			'password' => 'required|string|min:6|confirmed',
		]);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return \App\User
	 */
	protected function create(array $data) {
		$user = User::create([
			'name'     => $data['name'],
			'email'    => $data['email'],
			'password' => Hash::make($data['password']),
		]);

		if ($user) {
			$user->syncRoles(['Subscriber']); 		// provide a default Role

     		$profile = new Profile;					// Attach a new User Profile
        	$profile->user_id = $user->id;
   	        $profile->username = $data['username'];
          	$profile->save();

            $msg = 'User "' . $user->name . '" was successfully saved and enrolled as a Subscriber.';
            msgx(['success' => [$msg, true]]);
            $msg = 'Please contact <a href="/contact">The Administrator</a> to request additional Roles and Privileges.';
            msgx(['info' => [$msg, true]]);
        } else {
            $msg = 'User "' . $data['name'] . '" was NOT saved.';
            msgx(['failure' => [$msg, true]]);
        }

		return $user;
	}
}
