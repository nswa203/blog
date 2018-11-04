<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use GuzzleHttp\Client;
use App\Comment;
use App\Post;
use Image;
use Purifier;
use Session;
use Validator;

class CommentController extends Controller
{

    public function __construct() {
        $this->middleware('auth',['except' => 'store']);
        $this->middleware(function ($request, $next) {
            session(['zone' => 'Comments']);
            return $next($request);
        });
    }

    // This Query Builder searches our table/columns and related_tables/columns for each word/phrase.
    // The table is sorted (ascending or descending) and finally filtered.
    // It requires the custom queryHelper() function in Helpers.php.
    public function searchSortQuery($request) {
        $query = [
            'model'         => 'Comment',
            'searchModel'   => ['name', 'email', 'comment'],
            'searchRelated' => [
                'post' => ['title', 'slug', 'body', 'excerpt']
            ],
            'sortModel'   => [
                'i'       => 'd,id',                                                      
                'n'       => 'a,name',
                'e'       => 'a,email',
                't'       => 'a,comment',
                'c'       => 'd,created_at',
                'default' => 'i'                       
            ],            
        ];
        return queryHelper($query, $request);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $pager = pageSize($request, 'commentsIndex', 12, 4, 192, 4);    // size($request->pp), sessionTag, default, min, max, step
        $comments = $this->searchSortQuery($request)->paginate($pager['size']);
        $comments->pager = $pager;

        if ($comments && $comments->count() > 0) {

        } else {
            Session::flash('failure', 'No Post Comments were found.');
        }
        return view('manage.comments.index', ['comments' => $comments,
            'search' => $request->search, 'sort' => $request->sort]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $post_id) {
        $post = Post::findOrFail($post_id);

        // We use our own validator here as auto validation cannot handle error return from a POST method 
        $validator = Validator::make($request->all(), [
            'name'    => 'required|min:3|max:191',
            'email'   => 'required|email|min:5|max:191|',
            'comment' => 'required|min:8|max:10240',
        ]);
        if ($validator->fails()) {
            return redirect()->route('blog.single', $post->slug)->withErrors($validator)->withInput();
        }

        // We protect this public Form with a Captcha which protects us from Bots etc.
        // Google recaptcha account credentials were stored as ENV values.
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
            //dd($results);
            $myrc = $results->success;
        } else { $myrc = false; }
        if (!$myrc) {
            Session::flash('failure', "You're probably not human!");
            return redirect()->route('blog.single', $post->slug)->withInput();
        }

        // OK we are talking to a human.
        $comment = new Comment;
        $comment->name      = $request->name;
        $comment->email     = $request->email;
        $comment->comment   = Purifier::clean($request->comment);
        $comment->approved  = true;
        $comment->post()->associate($post);

        $myrc = $comment->save();

        if ($myrc) {
            $nick = explode(' ', $request->name);
            Session::flash('success', $nick[0] . ', your Comment for "' . $post->slug . '" was successfully saved.');
            return redirect()->route('blog.single', $post->slug);
        } else {
            Session::flash('failure', 'Your Comment for Post "' . $post_id . '" was NOT saved.');
            return redirect()->route('blog.single', $post->slug)->withInput();
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

        if ($comment) {
            $post = Post::find($comment->post_id);
            return view('manage.comments.edit', ['comment' => $comment, 'post' => $post]);
        } else {
            Session::flash('failure', 'Comment "' . $id . '" was NOT found.');
            return redirect()->route('comments.index');
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

        // We use our own validator here as auto validation cannot handle error return from a POST method 
        $validator = Validator::make($request->all(), [
            'comment'   => 'required|min:8|max:10240',
        ]);
        if ($validator->fails()) {
            return redirect()->route('comments.edit', $id)->withErrors($validator)->withInput();
        }

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

        if ($comment) {
            $post = Post::find($comment->post_id);
            return view('manage.comments.delete', ['comment' => $comment, 'post' => $post]);
        } else {
            Session::flash('failure', 'Comment "' . $id . '" was NOT found.');
            return redirect()->route('comments.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $comment = Comment::findOrFail($id);
        $post_id = $comment->post->id;
        $myrc = $comment->delete();
        
        if ($myrc) {
            Session::flash('success', 'Comment "' . $id . '" was successfully Deleted.');
            return redirect()->route('comments.index');
        } else {
            Session::flash('failure', 'Comment "' . $id . '" was NOT Deleted.');
            return redirect()->route('comments.delete', $id)->withInput();
        }
    }
    
}
