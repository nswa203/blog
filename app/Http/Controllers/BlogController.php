<?php

namespace App\Http\Controllers;

use App\Post;
use Session;

class BlogController extends Controller {

	public function getIndex() {
		$posts = Post::orderBy('id', 'desc')->where('status', '>=', '4')->paginate(5);

		if ($posts) {

		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('blog.index', ['posts' => $posts]);
	}

	public function getSingle($slug) {
		$post = Post::where('slug', '=', $slug)->where('status', '>=', '4')->first();

		if ($post) {
			// We only include "Approved" comments in this public view.
			// Comments are automatically set Approved on creation.
			// Comment approval status may be editted by an authorised User.  
			$post->comments=$post->comments->where('approved', '=', '1');
			return view('blog.single', ['post' => $post]);
		} else {
			Session::flash('failure', 'Blog Post "' . $slug . '" not found.');
			return redirect()->route('home');
		}
	}

}
