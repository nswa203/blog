<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Post;
use Session;

class BlogController extends Controller
{

	public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            session(['zone' => 'Blog']);
            return $next($request);
        });
	}

    // This Query Builder searches each table and each associated table for each word/phrase
    // It requires that SearchController pre loads Session('search_list')
	public function searchQuery($search = '') {
		$searchable1 = ['title', 'slug', 'image', 'body', 'excerpt'];
		$searchable2 = ['user' => ['name'], 'category' => ['name'], 'tags' => ['name'], 'comments' => ['email', 'name', 'comment' ]];
		$query = Post::select('*');

        if ($search !== '') {
            $search_list=session('search_list', []);
            foreach ($searchable1 as $column) {
                foreach ($search_list as $word) {
                    $query->orWhere($column, 'LIKE', '%' . $word . '%')->where('status', '>=', '4');
                }    
            }
            foreach ($searchable2 as $table => $columns) {
                foreach ($columns as $column) {
                    foreach ($search_list as $word) {
                        $query->orWhereHas($table, function($q) use ($column, $search, $word){
                            $q->where($column, 'LIKE', '%' . $word . '%')->where('status', '>=', '4');
                        }); 
                    }
                }
            }
        } 
        return $query;
	}

	public function getIndex(Request $request) {
		if ($request->search) {
			$posts = $this->searchQuery($request->search)->orderBy('id', 'desc')->paginate(5);
		} else {
			$posts = Post::where('status', '>=', '4')->orderBy('id', 'desc')->paginate(5);
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
