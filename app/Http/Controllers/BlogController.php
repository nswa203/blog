<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Album;
use App\File;
use App\Folder;
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
				'comments' 	=> ['email', 'name', 'comment' ],
				'folders'	=> ['name', 'slug', 'description'],
				'albums'	=> ['title', 'slug', 'description']
            ],
            'filter'		=>['status', '>=', '4']
        ];
        return search_helper($search, $query);
    }


	// We only include "Published" Posts in this public view.
	// The "public" filter is set in the above searchQuery
	public function index(Request $request) {
        $posts = $this->searchQuery($request->search)->orderBy('updated_at', 'desc')->paginate(4, ['*'], 'pageP');
		if ($posts && $posts->count() > 0) {

		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('pages.welcome', ['posts' => $posts, 'search' => $request->search]);
	}

}
