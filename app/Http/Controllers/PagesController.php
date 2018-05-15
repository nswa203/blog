<?php

namespace App\Http\Controllers;

use App\Post;
use Auth;

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
		$first = 'Nick';
		$last = 'Svonja';
		$email = 'nswa203@btinternet.com';
		$data = [];
		$data['fullname'] = $first . ' ' . $last;
		$data['email'] = $email;

		return view('pages.about')->with('data', $data);
	}

	public function getContact() {
		return view('pages.contact');
	}

	public function postContact() {

	}

}
