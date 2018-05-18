<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use Auth;
use Mail;
use Session;

class PagesController extends Controller {

	public function getIndex() {
		$posts = Post::orderBy('created_at', 'desc')->limit(4)->get();

		if ($posts) {

		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('pages.welcome', ['posts' => $posts]);
	}

	public function getAbout() {
		$first 	= 'Nick';
		$last 	= 'Svonja';
		$email 	= 'nswa203@btinternet.com';
		$data 	= [];
		$data['fullname'] = $first . ' ' . $last;
		$data['email'] = $email;

		return view('pages.about')->with('data', $data);
	}

	public function getContact() {
		return view('pages.contact');
	}

	public function postContact(Request $request) {

		$this->validate($request, [
			'name' 			=> 'required|min:2',
			'email' 		=> 'required|email',
			'subject'		=> 'required|min:2',
			'message' 		=> 'required|min:8',
		]); 
		
		$data=[
			'name' 			=> $request->name,
			'email' 		=> $request->email,
			'subject'		=> $request->subject,
			'bodyMessage'	=> $request->message
		];

		$myrc = Mail::send('emails.contact', $data, function($message) use ($data) {
			$message->from($data['email']);
			$message->to('nswa002@btinternet.com');
			$message->subject($data['subject']);
		});

		if (!$myrc) {
			Session::flash('success', 'Your eMail was successfully sent.');
		} else {
			Session::flash('failure', 'The eMail was NOT sent.');
		}
		return redirect()->route('home');
	}

}
