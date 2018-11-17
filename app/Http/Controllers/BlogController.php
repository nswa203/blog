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
    // The table is sorted (ascending or descending) and finally filtered.
    // It requires the custom queryHelper() function in Helpers.php.
    public function searchSortQuery($request) {
        $query = [
            'model'         => 'Post',
            'searchModel'   => ['title', 'slug', 'image', 'body', 'excerpt'],
            'searchRelated' => [
                'user'      => ['name'],
                'category'  => ['name'],
                'tags'      => ['name'],
                'comments'  => ['email', 'name', 'comment' ],
                'folders'   => ['name', 'slug', 'description'],
                'albums'    => ['title', 'slug', 'description']
            ],
            'sortModel'   => [
                'i'       => 'd,id',
                'u'       => 'd,updated_at',
                'default' => 'u'                       
            ],
            'filter'      =>['status', '>=', '4']
        ];
        return queryHelper($query, $request);
    }

	// We only include "Published" Posts in this public view.
	// The "public" filter is set in the above searchQuery
	public function index(Request $request) {
        $pager = pageSize($request, 'blogIndex', 4, 4, 48, 4);    // size($request->pp), sessionTag, default, min, max, step
        $posts = $this->searchSortQuery($request)->paginate($pager['size']);
        $posts->pager = $pager;

		if ($posts && $posts->count() > 0) {

		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('pages.welcome', ['posts' => $posts, 'search' => $request->search, 'sort' => $request->sort]);
	}

}
