<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Post;
use App\Folder;
use App\Album;
use Session;

class BlogController extends Controller
{

	public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            session(['zone' => 'Blog']);
            return $next($request);
        });
	}

    // This Query Builder searches our table/columns and related_tables/columns for each word/phrase.
    // It requires the custom search_helper() function in Helpers.php.
    // If you change Helpers.php you should do "dump-autoload". 
    public function searchQuery($search = '') {
        $query = [
            'model'         => 'Post',
            'searchModel'   => ['title', 'slug', 'image', 'body', 'excerpt'],
            'searchRelated' => [
				'user' 		=> ['name'],
				'category'  => ['name'],
				'tags' 		=> ['name'],
				'comments' 	=> ['email', 'name', 'comment' ]
            ],
            'filter'		=>['status', '>=', '4']
        ];
        return search_helper($search, $query);
    }

	public function getIndex(Request $request) {
        $posts = $this->searchQuery($request->search)->orderBy('id', 'desc')->paginate(5);
		if ($posts) {

		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('blog.index', ['posts' => $posts, 'search' => $request->search]);
	}

	public function getSinglePost($slug) {
		// We only include "Published" Posts in this public view.
		$post = Post::where('slug', '=', $slug)->where('status', '>=', '4')->first();

		if ($post) {
			// We only include "Approved" comments in this public view.
			// Comments are automatically set Approved on creation.
			// Comment approval status may be editted by an authorised User.  
			$post->comments=$post->comments->where('approved', '=', '1');
			return view('blog.singlePost', ['post' => $post]);
		} else {
			Session::flash('failure', 'Blog Post "' . $slug . '" is not available.');
			return redirect()->route('home');
		}
	}

	public function getSingleFolder($slug) {
		// We only include "Public" Folders in this public view.
		$folder = Folder::where('slug', '=', $slug)->where('status', '>=', '1')->first();

		if ($folder) {
			return view('blog.singleFolder', ['folder' => $folder]);
		} else {
			Session::flash('failure', 'Blog Folder "' . $slug . '" is not available.');
			return redirect()->route('home');
		}
	}

}
