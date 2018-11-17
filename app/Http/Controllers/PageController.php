<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Post;
use App\File;
use App\Folder;
use Auth;
use Mail;
use Purifier;
use Session;
use Validator;

use DB;

class PageController extends Controller
 {

    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
        	DB::enableQueryLog();
            session(['zone' => 'Blog']);
            return $next($request);
        });
    }

    // This Query Builder searches our table/columns and related_tables/columns for each word/phrase.
    // The table is sorted (ascending or descending) and finally filtered.
    // Looses searches are performed unless the search string is enclosed in '^'
    // It requires the custom queryHelper() function in Helpers.php.
    // $posts = paginateHelper($this->query(), $request, 12, 4, 192, 4);
    public function query($model=false) {
        if ($model == 'pca') {									// Posts with this Category name
	        $query = [
	            'model'         => 'Post',
	            'searchModel'   => [],
	            'searchRelated' => [
					'category' 	=> ['^name^'],
	            ],
                'sort'        => [
	                'u'       => 'd,updated_at',
	                'default' => 'u'                       
	            ],
	            'filter' 	  => ['status', '>=', '4']
        	];
        } elseif ($model == 'pta') {							// Posts with this Tag name
	        $query = [
	            'model'         => 'Post',
	            'searchModel'   => [],
	            'searchRelated' => [
					'tags' 		=> ['^name^'],
	            ],
                'sort'        => [
	                'u'       => 'd,updated_at',
	                'default' => 'u'                       
	            ],	            
	            'filter'	  => ['status', '>=', '4']
        	];        	
        } elseif ($model == 'pus') {							// Posts with this User name
	        $query = [
	            'model'         => 'Post',
	            'searchModel'   => [],
	            'searchRelated' => [
					'user' 		=> ['^name^'],
					'comments' 	=> ['^name^'],
	            ],
                'sort'        => [
	                'u'       => 'd,updated_at',
	                'default' => 'u'                       
	            ],	            
	            'filter'	  => ['status', '>=', '4']
        	];






        } elseif ($model == 'f') {
	        $query = [
	            'model'         => 'Folder',
	            'searchModel'   => ['slug'],
	            'searchRelated' => [],
	            'filter'		=> ['status', '>=', '1']
        	];
        } elseif ($model == 'fi') {
	        $query = [
	            'model'         => 'File',
	            'searchModel'   => ['id'],
	            'searchRelated' => [],
	            'filter'		=> ['status', '>=', '4']
        	];        	              	        	        	
        } elseif ($model == 'files') {
        	$query = [
	            'model'         => 'File',
	            'searchModel'   => ['^folder_id^'],
	            'searchRelated' => [],
                'sort'   => [
	                'i'       => 'd,id',
   	                't'       => 'a,title',
	                's'       => 'd,size',
	                'p'       => 'd,published_at',
	                'default' => 'i'                       
	            ],
  	            'filter'		=> ['status', '>=', '4']
        	];
        } elseif ($model == 'category') {
        	$query = [
	            'model'         => 'Category',
	            'searchModel'   => [],
	            'searchRelated' => [
					'posts'		=> ['^status^'],
	            ],
                'sort'   => [
	                'n'       => 'a,name',
	                'default' => 'n'                       
	            ],	            	            
        	];
        } elseif ($model == 'tag') {
        	$query = [
	            'model'         => 'Tag',
	            'searchModel'   => [],
	            'searchRelated' => [
					'posts'		=> ['^status^'],
	            ],
                'sort'        => [
	                'n'       => 'a,name',
	                'default' => 'n'                       
	            ],	            
        	];
        } else {        	
	        $query = [											// Any published Post with these search values
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
                'sort'        => [
	                'u'       => 'd,updated_at',
	                'default' => 'u'                       
	            ],
	            'filter'	  => ['status', '>=', '4']
	        ];
        }
        return $query;
    }

	// We only include "Published" Posts in this public view.
	// The "public" filter is set in the above searchQuery
	public function indexPost(Request $request) {
		if ($request->pal) {								
			$searchType = 'pal';							// Posts with these Albums
			$request->search = $request->pal;
		} elseif ($request->pca) {								
			$searchType = 'pca';							// Posts with this Category
			$request->search = '"'.$request->pca.'"';
		} elseif ($request->pfo) {
			$searchType = 'pfo';							// Posts with these Folders
			$request->search = $request->pfo;
		} elseif ($request->pfi) {
			$searchType = 'pfi';							// Posts with these Files
			$request->search = $request->pfi;
		} elseif ($request->pph) {
			$searchType = 'pph';							// Posts with these Photos
			$request->search = $request->pph;			
		} elseif ($request->pta) {
			$searchType = 'pta';							// Posts with this Tag
			$request->search = '"'.$request->pta.'"';
		} elseif ($request->pus && Auth::check()) {	
			$searchType = 'pus';							// Posts with current User
			$request->search = '"'.Auth::user()->name.'"';
		} else {
			$searchType = false;
		}
        $posts = paginateHelper($this->query($searchType), $request, 4, 4, 48, 2);

		if ($posts && $posts->count() > 0) {
			//dd($this->query($searchType), DB::getQueryLog(), $posts);
		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('blog.index', ['posts' => $posts, 'search' => $request->search]);
	}

	// We only include "Published" Posts in this public view.
	// The "public" filter is set in the above searchQuery
	// We are only selecting Tags which have "Published" Posts.  
	public function showHome(Request $request) {
        $posts = paginateHelper($this->query(), $request, 4, 1, 48, 1, true); // size($request->pp), default, min, max, step, force
        $request->search  = '4';
        $request->sort    = false; 
        $categories = queryHelperTest($this->query('category'), $request)->get();
        $tags       = queryHelperTest($this->query('tag'     ), $request)->get();

//dd($request);
//$tagsFile   	  = queryHelperTest($this->query('tagFile'       ), $request)->get();
//$request->search  = '1'; 										      // Public Status (Folders)
//$categoriesFolder = queryHelperTest($this->query('categoryFolder'), $request)->get();
//$categoriesAll = $categories->merge($categoriesFolder);
//foreach ($categoriesAll as $category) {
//	echo $category->name.' '.$category->posts->count().' '.$category->folders->count().'<br>';
//}
//dd($categories, $categoriesFolder, $categoriesAll);
   		$data = [];
		$data['name'] = env('APP_NAME');

		if ($posts && $posts->count() > 0) {

		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('pages.welcome', ['posts' => $posts, 'categories' => $categories, 'tags' => $tags,
			'search' => $request->search, 'data' => $data]);
	}

	public function showAbout() {
		$data = [];
		$data['name'] = env('APP_NAME');
		return view('pages.about')->with('data', $data);
	}

	public function showContact() {
		$data = [];
		$data['owner'] = env('APP_OWNER');
		$data['key'  ] = myConstants('CAPTCHA_SITEKEY');
		return view('pages.contact')->with('data', $data);
	}

	public function postContact(Request $request) {
        // We use our own validator here as auto validation cannot handle error return from a POST method 
        $validator = Validator::make($request->all(), [
			'name' 		=> 'required|min:3|max:191',
			'email' 	=> 'required|email|min:5|max:191',
			'subject'	=> 'required|min:3|max:191',
			'message' 	=> 'required|min:8|max:2048',
        ]);
        if ($validator->fails()) {
            return redirect()->route('blog.contact')->withErrors($validator)->withInput();
        }

        // We protect this public Form with a Captcha which protects us from Bots etc.
        // Google recaptcha account credentials have been stored as ENV values.
        $captcha_server = myConstants('CAPTCHA_SERVER');
        $captcha_secret = myConstants('CAPTCHA_SECRET');

        $token = $request->input('g-recaptcha-response');
        if ($token) {
            $client = new Client();
            $response = $client->post($captcha_server, [
                'form_params' => [
                    'secret' => $captcha_secret,
                    'response' => $token
                ]
            ]);
            $results = json_decode($response->getBody()->getContents());
            $myrc = $results->success;
        } else { $myrc = false; }
        if (!$myrc) {
            Session::flash('failure', "You're probably not human!");
            return redirect()->route('blog.contact')->withInput();
        }

		$data = [
			'name' 			=> $request->name,
			'email' 		=> $request->email,
			'subject'		=> $request->subject,
			'bodyMessage'	=> Purifier::clean($request->message),
		];

		$myrc = Mail::send('emails.contact', $data, function($message) use ($data) {
			$message->from($data['email']);
			$message->to('nswa002@btinternet.com');
			$message->subject($data['subject']);
		});

		if (!$myrc) {
			$nick = explode(' ', $request->name);
			Session::flash('success', $nick[0].', your eMail was successfully sent.');
			return redirect()->route('home');
		} else {
			Session::flash('failure', 'The eMail was NOT sent.');
            return redirect()->route('blog.contact')->withInput();
		}
	}

	// We only include "Published" Posts in this public view.
	// $select may be an id OR a slug
	public function showPost($select) {
		if (is_int($select)) {
			$post = Post::where('id', $select)->where('status', '>=', '4')->first();
		} else {
			$post = false;
		}
		if (!$post) {
			$post = Post::where('slug', $select)->where('status', '>=', '4')->first();
		}

		if ($post) { 
			// We only include "Approved" comments in this public view.
			// Comments are automatically set Approved on creation.
			// Comment approval status may be edited by an authorised User. 
			$post->comments = $post->comments->where('approved', '1')->sortByDesc('updated_at');
			return view('blog.showPost', ['post' => $post]);
		} else {
			Session::flash('failure', 'Blog Post "' . $select . '" is not available.');
			return redirect()->back();
		}
	}

	// We only include "Public" Folders & public files in this public view.
	public function showFolder(Request $request, $select) {
		//dd($request, $select, $request->search);
		if (is_int($select)) {
			$folder = Folder::where('id', $select)->where('status', '>=', '1')->with(['files' => function ($q) {
				$q->where('status', '>=', '4');
			}])->first();
		} else {
			$folder = false;
		}
		if (!$folder) {
			$folder = Folder::where('slug', $select)->where('status', '>=', '1')->with(['files' => function ($q) {
				$q->where('status', '>=', '4');
			}])->first();		}

		$list['f'] = fileStatus();
        $list['d'] = folderStatus();   

		if ($folder) {
			$request->search = $folder->id;
	        $files = paginateHelper($this->query('files'), $request, 12, 4, 256, 4);
	        $request->search = '4';
            $tagsPost = queryHelperTest($this->query('tag'), $request)->get();
	        $request->search = null;
			return view('blog.showFolder', ['folder' => $folder, 'files' => $files, 'tagsPost' => '$tagsPost', 
				'search' => $request->search, 'sort' => $request->sort, 'list' => $list]);
		} else {
			Session::flash('failure', 'Blog Folder "' . $select . '" is not available.');
			return redirect()->back();
		}
	}

	// We only include "Published" Files within "Public" Folders in this public view.
	public function showFile($id) {
		$file = File::where('id', $id)->with('folder')->first();

		if ($file && $file->status >= 4 && $file->folder->status >= 1) {
			return view('blog.showFile', ['file' => $file]);
		} else {
			Session::flash('failure', 'Blog File "' . $id . '" is not available.');
			return redirect()->back();
		}
	}		

}
