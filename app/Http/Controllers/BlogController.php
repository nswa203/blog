<?php

namespace App\Http\Controllers;

use App\Post;

class BlogController extends Controller {

	public function getIndex() {
		$posts = Post::orderBy('id', 'desc')->paginate(10);

		if ($posts) {

		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('blog.index', ['posts' => $posts]);
	}

	public function getSingle($slug) {
		$post = Post::where('slug', '=', $slug)->first();

		// We only include "Approved" comments in this public view.
		// Comments are automatically set Approved on creation.
		// Comment approval status may be editted by an authorised User.  
		$post->comments=$post->comments->where('approved', '=', '1');

		return view('blog.single', ['post' => $post]);
	}

}
