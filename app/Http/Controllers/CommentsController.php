<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;
use App\Post;
use Session;

class CommentsController extends Controller
{

    public function __contruct() {
        $this->middleware('auth',['except' => 'store']);
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
        $comment->comment   = $request->comment;
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

        } else {
            Session::flash('failure', 'Post Comment ' . $id . ' was NOT found.');
        }
        return view('comments.edit', ['comment'=>$comment, 'post'=>$post]);
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

        $comment->comment   = $request->comment;
        if ($request->approved) {
            $comment->approved = '1';
        } else {
            $comment->approved = '0';
         }   

        $myrc = $comment->save();

        if ($myrc) {
            Session::flash('success', 'Your Comment was successfully saved.');
        } else {
            Session::flash('failure', 'Your Comment was NOT saved.');
        }
        return redirect()->route('posts.show',[$comment->post->id]);
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
            
        } else {
            Session::flash('failure', 'Post Comment ' . $id . ' was NOT found.');
        }
        return view('comments.delete', ['comment'=>$comment, 'post'=>$post]);
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
        $myrc = $comment->delete();

       if ($comment) {
            Session::flash('success', 'Post Comment was successfully Deleted.');
        } else {
            Session::flash('failure', 'Post Comment ' . $id . ' was NOT Deleted.');
        }
        return redirect()->route('posts.show', $post_id);
    }
}
