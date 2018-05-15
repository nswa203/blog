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

		return view('blog.single', ['post' => $post]);
	}

}
