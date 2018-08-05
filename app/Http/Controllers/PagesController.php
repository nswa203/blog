<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use GuzzleHttp\Client;
use App\File;
use App\Folder;
use App\Post;
use App\Tag;
use Auth;
use Mail;
use Session;
use Purifier;

class PagesController extends Controller
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
	public function getHomePost(Request $request) {
        $posts = $this->searchQuery($request->search)->orderBy('updated_at', 'desc')->paginate(4, ['*'], 'pageP');
		if ($posts && $posts->count() > 0) {

		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('pages.welcome', ['posts' => $posts, 'search' => $request->search]);
	}

	// We only include "Published" Posts in this public view.
	// The "public" filter is set in the above searchQuery
	public function getIndexPost(Request $request) {
        $posts = $this->searchQuery($request->search)->orderBy('updated_at', 'desc')->paginate(4, ['*'], 'pageP');
		if ($posts && $posts->count() > 0) {
		
		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('blog.index', ['posts' => $posts, 'search' => $request->search]);
	}

	// We only include "Published" Posts in this public view.
	public function getIndexTagPost($id) {
		$tag = Tag::findOrFail($id);
        $posts = $tag->posts() ->orderBy('updated_at', 'desc')->paginate(4, ['*'], 'pageP');

		if ($posts && $posts->count() > 0) {

		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('blog.index', ['posts' => $posts, 'search' => $tag->name]);
	}


	// We only include "Published" Posts in this public view.
	public function getSinglePost($slug) {
		$post = Post::where('slug', $slug)->where('status', '>=', '4')->first();
		if ($post) { 
			// We only include "Approved" comments in this public view.
			// Comments are automatically set Approved on creation.
			// Comment approval status may be edited by an authorised User.  
			$post->comments = $post->comments->where('approved', '1');
			return view('blog.singlePost', ['post' => $post]);
		} else {
			Session::flash('failure', 'Blog Post "' . $slug . '" is not available.');
			return redirect()->route('home');
		}
	}

	// We only include "Public" Folders in this public view.
	public function getSingleFolder($slug) {
		$folder = Folder::where('slug', $slug)->where('status', '>=', '1')->first();
		if ($folder) {
			return view('blog.singleFolder', ['folder' => $folder]);
		} else {
			Session::flash('failure', 'Blog Folder "' . $slug . '" is not available.');
			return redirect()->route('home');
		}
	}

	// We only include "Published" Files within "Public" Folders in this public view.
	public function getSingleFile($id) {
		$file = File::where('id', $id)->with('folder')->first();
		if ($file && $file->status >= 4 && $file->folder->status >= 1) {
			return view('blog.singleFile', ['file' => $file]);
		} else {
			Session::flash('failure', 'Blog File "' . $id . '" is not available.');
			return redirect()->route('home');
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
        $token = $request->input('g-recaptcha-response');
        if ($token) {
            $client = new Client();
            $response = $client->post(env('CAPTCHA_SERVER'), [
                'form_params' => [
                    'secret' => env('CAPTCHA_SECRET'),
                    'response' => $token
                ]
            ]);
            $results = json_decode($response->getBody()->getContents());
            $myrc = $results->success;
        } else { $myrc = false; }
        if (!$myrc) {
            Session::flash('failure', "You're probably not human!");
            return Redirect::back()->withInput();
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
			Session::flash('success', 'Your eMail was successfully sent.');
			return redirect()->route('home');
		} else {
			Session::flash('failure', 'The eMail was NOT sent.');
            return Redirect::back()->withInput();
		}
	}

}
