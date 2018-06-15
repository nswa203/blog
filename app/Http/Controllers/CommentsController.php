<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;
use App\Post;
use Session;
use Purifier;


function searchQuery($search = '') {
    $searchable1 = ['name', 'email', 'comment'];
    $searchable2 = [];
    $query = Comment::select('*');

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


class CommentsController extends Controller
{

    public function __construct() {
        $this->middleware('auth',['except' => 'store']);
        $this->middleware(function ($request, $next) {
            session(['zone' => 'Comments']);
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->search) {
            $comments = searchQuery($request->search)->orderBy('post_id', 'desc')->paginate(10);
        } else {
            $comments = Comment::orderBy('post_id', 'desc')->paginate(5);
        }   

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
            'name'      => 'required|min:3|max:191',
            'email'     => 'required|email|max:191|',
            'comment'   => 'required|min:8|max:2048',
        ]);

        $post = Post::find($post_id);

        $comment = new Comment;
        $comment->name      = $request->name;
        $comment->email     = $request->email;
        $comment->comment   = Purifier::clean($request->comment);
        $comment->approved  = true;
        $comment->post()->associate($post);

        $myrc = $comment->save();

        if ($myrc) {
            Session::flash('success', 'Your Comment was successfully added.');
        } else {
            Session::flash('failure', 'Your Comment was NOT saved.');
        }
        return redirect()->route('blog.single',[$post->slug]);
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
