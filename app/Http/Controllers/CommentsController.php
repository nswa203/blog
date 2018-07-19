<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use GuzzleHttp\Client;
use App\Comment;
use App\Post;
use Session;
use Purifier;

class CommentsController extends Controller
{

    public function __construct() {
        $this->middleware('auth',['except' => 'store']);
        $this->middleware(function ($request, $next) {
            session(['zone' => 'Comments']);
            return $next($request);
        });
    }

    // This Query Builder searches our table/columns and related_tables/columns for each word/phrase.
    // It requires the custom search_helper() function in Helpers.php.
    // If you change Helpers.php you should do "dump-autoload". 
    public function searchQuery($search = '') {
        $query = [
            'model'         => 'Comment',
            'searchModel'   => ['name', 'email', 'comment'],
            'searchRelated' => [
                'post' => ['title', 'slug', 'body', 'excerpt']
            ]
        ];
        return search_helper($search, $query);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $comments = $this->searchQuery($request->search)->orderBy('id', 'desc')->paginate(10);
        if ($comments) {

        } else {
            Session::flash('failure', 'No Post Comments were found.');
        }
        return view('manage.comments.index', ['comments' => $comments, 'search' => $request->search]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $post_id) {
        $this->validate($request, [
            'name'    => 'required|min:3|max:191',
            'email'   => 'required|email|min:5|max:191|',
            'comment' => 'required|min:8|max:2048',
        ]);

        // We protect this public Form with a Captcha which protects us from Bots etc.
        // Google recaptcha account credentials were stored as ENV values. 
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

        // OK we are talking to a human.
        $post = Post::find($post_id);

        $comment = new Comment;
        $comment->name      = $request->name;
        $comment->email     = $request->email;
        $comment->comment   = Purifier::clean($request->comment);
        $comment->approved  = true;
        $comment->post()->associate($post);

        $myrc = $comment->save();

        if ($myrc) {
            Session::flash('success', 'Your Comment for "' . $post->slug . '" was successfully saved.');
            return redirect()->route('blog.single', $post->slug);
        } else {
            Session::flash('failure', 'Your Comment for Post "' . $post_id . '" was NOT saved.');
            return Redirect::back()->withInput();
        }
    }    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $comment = Comment::find($id);
        $post = Post::find($comment->post_id);

        if ($comment) {
            return view('manage.comments.edit', ['comment' => $comment, 'post' => $post]);
        } else {
            Session::flash('failure', 'Comment "' . $id . '" was NOT found.');
            return Redirect::back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $comment = Comment::find($id);

        $this->validate($request, [
            'comment'   => 'required|min:8|max:2048',
        ]);

        $post = Post::find($comment->post_id);

        $comment->comment = Purifier::clean($request->comment);;
        if ($request->approved) {
            $comment->approved = '1';
        } else {
            $comment->approved = '0';
        }   

        $myrc = $comment->save();

        if ($myrc) {
            Session::flash('success', 'Comment "' . $id .'" was successfully saved.');
            return redirect()->route('posts.show', [$comment->post->id]);
        } else {
            Session::flash('failure', 'Comment "' . $id . '" was NOT saved.');
            return redirect()->route('comments.edit', $id)->withInput();            
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id) {
        $comment = Comment::find($id);
        $post = Post::find($comment->post_id);

        if ($comment) {
            return view('manage.comments.delete', ['comment' => $comment, 'post' => $post]);
        } else {
            Session::flash('failure', 'Comment "' . $id . '" was NOT found.');
            return Redirect::back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $comment = Comment::find($id);
        $post_id = $comment->post->id;

       if ($comment) {
            $myrc = $comment->delete();
            Session::flash('success', 'Comment "' . $id . '" was successfully Deleted.');
            return redirect()->route('posts.show', $post_id);
        } else {
            Session::flash('failure', 'Comment "' . $id . '" was NOT Deleted.');
            return Redirect::back();
        }
    }
    
}
