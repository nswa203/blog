<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use Session;

function searchQuery($search = '') {
	$searchable1 = ['title', 'slug', 'image', 'body', 'excerpt'];
	$searchable2 = ['user' => ['name'], 'category' => ['name'], 'tags' => ['name']];
	$query = Post::select('*')->with('user')->with('category');

    if ($search !== '') {
        foreach ($searchable1 as $column) {
            $query->orWhere($column, 'LIKE', '%' . $search . '%');
        }
        foreach ($searchable2 as $table => $columns) {
            foreach ($columns as $column) {
                $query->orWhereHas($table, function($q) use ($column, $search){
                    $q->where($column, 'LIKE', '%' . $search . '%');
                }); 
            }
        }
    }  
    return $query;
}

class BlogController extends Controller {
	public function __construct(Request $request)
	{
        $this->middleware(function ($request, $next) {
            session(['zone' => 'Blog']);
            return $next($request);
        });
	}

	public function getIndex(Request $request) {
		if ($request->search) {
			$posts = searchQuery($request->search)->orderBy('id', 'desc')->where('status', '>=', '4')->paginate(5);
		} else {
			$posts = Post::orderBy('id', 'desc')->where('status', '>=', '4')->paginate(5);
        }

		if ($posts) {

		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('blog.index', ['posts' => $posts, 'search' => $request->search]);
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
