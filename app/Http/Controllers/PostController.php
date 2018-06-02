<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Tag;
use App\Category;
use Session;
use Purifier;
use Image;
use Storage;

class PostController extends Controller {
	/**
	 * We lock down the complete PostController here using the __construct()
	 * which is like an init function for instantiation of an object of this class.
	 * Lock-down is achieved with middleware that ensures only authorised (Logged In)
	 * users have access.
	 */
	public function __construct() {
		$this->middleware('auth');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$posts = Post::orderBy('id', 'desc')->paginate(10);

		if ($posts) {

		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('manage.posts.index', ['posts' => $posts]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		// URI=http://blog/posts/create

		$categories=Category::orderBy('name','asc')->pluck('name','id');
		$tags=Tag::orderBy('name','asc')->pluck('name','id');
		return view('manage.posts.create',['categories'=>$categories,'tags'=>$tags]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		$this->validate($request, [
			'title' 			=> 'required|min:8|max:191',
			'slug' 				=> 'required|alpha_dash|min:5|max:191|unique:posts,slug',
			'category_id' 		=> 'required|integer',
			'tags'				=> 'array',
			'tags.*'			=> 'integer',
			'body' 				=> 'required',
			'featured_image' 	=> 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
		]);

		$post = new Post;
		$post->title 		= $request->title;
		$post->slug 		= $request->slug;
		$post->category_id 	= $request->category_id;
		$post->body 		= Purifier::clean($request->body);

		if ($request->hasFile('featured_image')) {
			$image=$request->file('featured_image');
			$filename=time().'.'.$image->getClientOriginalExtension();
			$location=public_path('images\\'.$filename);
			Image::make($image)->resize(800, null, function ($constraint) {$constraint->aspectRatio();})->save($location);
			$post->image = $filename;
		}

		$myrc = $post->save();

		$myrc2 = $post->tags()->sync($request->tags, false);

		if ($myrc and $myrc2) {
			Session::flash('success', 'The blog Post was successfully saved.');
		} else {
			Session::flash('failure', 'The blog Post was NOT saved.');
		}
		return redirect()->route('manage.posts.show', $post->id);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		$post = Post::find($id);

		if ($post) {

		} else {
			Session::flash('failure', 'Blog Post ' . $id . ' was NOT found.');
		}
		return view('manage.posts.show', ['post' => $post]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		$post = Post::find($id);
		$categories=Category::orderBy('name','asc')->pluck('name','id');
		$tags=Tag::orderBy('name','asc')->pluck('name','id');

		if ($post) {

		} else {
			Session::flash('failure', 'Blog Post ' . $id . ' was NOT found.');
		}
		return view('manage.posts.edit', ['post'=>$post, 'categories'=>$categories,'tags'=>$tags]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		$post = Post::find($id);

		$this->validate($request, [
				'title' 			=> 'required|min:8|max:191',
				'slug' 				=> "required|alpha_dash|min:5|max:191|unique:posts,slug,$id",
				'category_id' 		=> 'required|integer',
				'tags'				=> 'array',
				'tags.*'			=> 'integer',				
				'body' 				=> 'required',
				'featured_image' 	=> 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
			]);

		$post->title 		= $request->title;
		$post->slug 		= $request->slug;
		$post->category_id 	= $request->category_id;
		$post->body 		= Purifier::clean($request->body);

		if ($request->hasFile('featured_image')) {
			$oldFilename = $post->image;

			$image=$request->file('featured_image');
			$filename=time().'.'.$image->getClientOriginalExtension();
			$location=public_path('images\\'.$filename);
			$myrc=Image::make($image)->resize(800, null, function ($constraint) {$constraint->aspectRatio();})->save($location);
			$post->image = $filename;
			$msgOK=' and its image file ' . $image->getClientOriginalName() . ' were ';
		} elseif ($request->delete_image) {
			$oldFilename=$post->image;
			$post->image = null;
			$msgOK=' and its image file ' . $oldFilename . ' were ';
		} else {
			$oldFilename=false;
			$msgOK=' ';
		}

		$myrc = $post->save();
		$myrc2 = $post->tags()->sync($request->tags, true);

		if ($myrc and $myrc2) {
			if ($oldFilename) {
				Storage::delete($oldFilename);
			}
			Session::flash('success', 'Blog Post ' . $id . $msgOK . 'successfully updated.');
		} else {
			Session::flash('failure', 'The blog Post was NOT updated.');
		}

		return redirect()->route('manage.posts.show', $post->id);
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id) {
        $post = Post::find($id);

        if ($post) {
            
        } else {
            Session::flash('failure', 'Post ' . $id . ' was NOT found.');
        }
        return view('manage.posts.delete', ['post'=>$post]);
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		$post = Post::find($id);

		$post->tags()->detach();
		$myrc = $post->delete();

		if ($myrc) {
			if ($post->image) {
				Storage::delete($post->image);
				$msgOK = ' and its image file ' . $post->image . ' were ';
			} else {
				$msgOK = ' was ';
			}
			Session::flash('success', 'Blog Post ' . $id . $msgOK . 'successfully deleted.');
		} else {
			Session::flash('failure', 'The blog Post was NOT deleted.');
		}
		return redirect()->route('manage.posts.index');
	}
}
