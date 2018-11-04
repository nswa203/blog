<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use GuzzleHttp\Client;
use App\Category;
use App\File;
use App\Folder;
use App\Post;
use App\Tag;
use Auth;
use Mail;
use Purifier;
use Session;

class PageController extends Controller
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
    public function searchQuery($search = '', $op=false) {
        if ($op == 'pc') {
        	$search = '"' . $search . '"';
	        $query = [
	            'model'         => 'Post',
	            'searchModel'   => [],
	            'searchRelated' => [
					'category' 	=> ['name'],
	            ],
	            'filter' 		=> ['status', '>=', '4']
        	];
        } elseif ($op == 'f') {
        	$search = '"' . $search . '"';
	        $query = [
	            'model'         => 'Folder',
	            'searchModel'   => ['slug'],
	            'searchRelated' => [],
	            'filter'		=> ['status', '>=', '1']
        	];
        } elseif ($op == 'fi') {
        	$search = '"' . $search . '"';
	        $query = [
	            'model'         => 'File',
	            'searchModel'   => ['id'],
	            'searchRelated' => [],
	            'filter'		=> ['status', '>=', '4']
        	];        	              	        	        	
        } elseif ($op == 'pt') {
        	$search = '"' . $search . '"';
	        $query = [
	            'model'         => 'Post',
	            'searchModel'   => [],
	            'searchRelated' => [
					'tags' 		=> ['name'],
	            ],
	            'filter'		=>['status', '>=', '4']
        	];        	
        } elseif ($op == 'pu') {
        	$search = '"' . $search . '"';
	        $query = [
	            'model'         => 'Post',
	            'searchModel'   => [],
	            'searchRelated' => [
					'user' 		=> ['name'],
					'comments' 	=> ['name'],
	            ],
	            'filter'		=> ['status', '>=', '4']
        	];
        } elseif ($op == 'category') {
        	$query = [
	            'model'         => 'Category',
	            'searchModel'   => [],
	            'searchRelated' => [
					'posts'		=> ['status'],
	            ],
        	];
        } elseif ($op == 'tag') {
        	$query = [
	            'model'         => 'Tag',
	            'searchModel'   => [],
	            'searchRelated' => [
					'posts'		=> ['status'],
	            ],
        	];
        } else {        	
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
	            'filter'		=> ['status', '>=', '4']
	        ];
        }
        return search_helper2($search, $query);
    }

	// We only include "Published" Posts in this public view.
	// The "public" filter is set in the above searchQuery
	// We are only selecting Tags which have "Published" Posts.  
	public function getHomePost(Request $request) {
        $posts      = $this->searchQuery($request->search)->with('tags')->orderBy('updated_at', 'desc')->paginate(4, ['*'], 'pageP');
        $tags       = $this->searchQuery('4', 'tag')     ->orderBy('name', 'asc')->get();
        $categories = $this->searchQuery('4', 'category')->orderBy('name', 'asc')->get();
		if ($posts && $posts->count() > 0) {

		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('pages.welcome', ['posts' => $posts, 'categories' => $categories, 'tags' => $tags, 'search' => $request->search]);
	}

	// We only include "Published" Posts in this public view.
	// The "public" filter is set in the above searchQuery
	public function getIndexPost(Request $request) {
		if ($request->pc) {
			$request->search = $request->pc;
        	$posts = $this->searchQuery($request->search, 'pc')->orderBy('updated_at', 'desc')->paginate(4,  ['*'], 'pagePC');
		} elseif ($request->pf) {
			$request->search = $request->pf;
        	$posts = $this->searchQuery($request->search, 'pf')->orderBy('updated_at', 'desc')->paginate(4,  ['*'], 'pagePF');
		} elseif ($request->pfi) {
			$request->search = $request->pfi;
        	$posts = $this->searchQuery($request->search,'pfi')->orderBy('updated_at', 'desc')->paginate(4,  ['*'], 'pagePFI');   	     	
		} elseif ($request->pt) {
			$request->search = $request->pt;
        	$posts = $this->searchQuery($request->search, 'pt')->orderBy('updated_at', 'desc')->paginate(4,  ['*'], 'pagePT');
		} elseif ($request->pu && Auth::check()) {			
			$request->search = Auth::user()->name;
        	$posts = $this->searchQuery($request->search, 'pu')->orderBy('updated_at', 'desc')->paginate(10, ['*'], 'pagePU');
		} else {
	        $posts = $this->searchQuery($request->search      )->orderBy('updated_at', 'desc')->paginate(4,  ['*'], 'pageP');
		}
		if ($posts && $posts->count() > 0) {
		
		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('blog.index', ['posts' => $posts, 'search' => $request->search]);
	}

	// We only include "Published" Posts in this public view.
	// The "public" filter is set in the above searchQuery
	// This in NOT a publlic method as it may be used to identify a User's name...
	// ... so its protected behind an Administrator route.
	// The public version of this (above) only lists the Logged in User's Posts.
	// The Post search facility can search on User->name but is also protected. 
	public function getIndexUserPost(Request $request, $user) {
		$request->search = $user;
        $posts = $this->searchQuery($request->search, 'pu')->orderBy('updated_at', 'desc')->paginate(4, ['*'], 'pagePU');
   		if ($posts && $posts->count() > 0) {
		
		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('blog.index', ['posts' => $posts, 'search' => $request->search]);
	}

	// We only include "Published" Posts in this public view.
	public function getSinglePostBySlug($slug) {
		$post = Post::where('slug', $slug)->where('status', '>=', '4')->first();
		if ($post) { 
			// We only include "Approved" comments in this public view.
			// Comments are automatically set Approved on creation.
			// Comment approval status may be edited by an authorised User.  
			$post->comments = $post->comments->where('approved', '1');
			return view('blog.singlePost', ['post' => $post]);
		} else {
			Session::flash('failure', 'Blog Post "' . $slug . '" is not available.');
			return redirect()->back();
		}
	}

	// We only include "Published" Posts in this public view.
	public function getSinglePost($id) {
		$post = Post::where('id', $id)->where('status', '>=', '4')->first();
		if ($post) { 
			// We only include "Approved" comments in this public view.
			// Comments are automatically set Approved on creation.
			// Comment approval status may be edited by an authorised User.  
			$post->comments = $post->comments->where('approved', '1');
			return view('blog.singlePost', ['post' => $post]);
		} else {
			Session::flash('failure', 'Blog Post "' . $id . '" is not available.');
			return redirect()->back();
		}
	}

	// We only include "Public" Folders in this public view.
	public function getSingleFolder($slug) {
		$folder = Folder::where('slug', $slug)->where('status', '>=', '1')->first();
		if ($folder) {
			return view('blog.singleFolder', ['folder' => $folder]);
		} else {
			Session::flash('failure', 'Blog Folder "' . $slug . '" is not available.');
			return redirect()->back();
		}
	}

	// We only include "Published" Files within "Public" Folders in this public view.
	public function getSingleFile($id) {
		$file = File::where('id', $id)->with('folder')->first();
		if ($file && $file->status >= 4 && $file->folder->status >= 1) {
			return view('blog.singleFile', ['file' => $file]);
		} else {
			Session::flash('failure', 'Blog File "' . $id . '" is not available.');
			return redirect()->back();
		}
	}

	public function getAbout() {
		$data = [];
		$data['name']  = env('APP_OWNER');
		$data['email'] = env('APP_EMAIL');
		return view('pages.about')->with('data', $data);
	}

	public function getContact() {
		$data = [];
		$data['name']  = env('APP_OWNER');
		$data['email'] = env('APP_EMAIL');		
		return view('pages.contact')->with('data', $data);
	}

	public function postContact(Request $request) {
		$this->validate($request, [
			'name' 		=> 'required|min:3|max:191',
			'email' 	=> 'required|email|min:5|max:191',
			'subject'	=> 'required|min:3|max:191',
			'message' 	=> 'required|min:8|max:2048',
		]);

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
   			return redirect()->back()->withInput();
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
   			return redirect()->back()->withInput();
		}
	}

}
