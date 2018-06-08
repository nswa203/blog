<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Tag;
use App\Category;
use App\User;
use Session;
use Purifier;
use Image; 
use Storage;

function status($default = -1) {
	$status = [
		'4' => 'Published',
		'3' => 'Under Review',
		'2' => 'In Draft',
		'1' => 'Withheld',
		'0' => 'Dead',
	];
	if ($default >= 0) { $status[$default] = '*' . $status[$default]; }
	return $status;
}

class PostController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		$posts = Post::orderBy('id', 'desc')->with('user')->paginate(10);
        $posts->status_names = ['Dead', 'Witheld', 'In Draft', 'Under Review', 'Published', 'Test'];	

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

		$categories = Category::orderBy('name', 'asc')->pluck('name', 'id');
		$tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');
		$users = User::orderBy('name', 'asc')->pluck('name', 'id');
		$post = new Post;

		return view('manage.posts.create', ['post' => $post,
			'categories' => $categories, 'tags' => $tags, 'users' => $users, 'status_list' => status(2)]);
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
			'category_id' 		=> 'required|integer|exists:categories,id',
			'featured_image' 	=> 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
			'body' 				=> 'required|min:1',
			'excerpt' 			=> 'sometimes',
			'author_id' 		=> 'sometimes|integer|exists:users,id',
			'status' 			=> 'required|integer|min:0|max:4',
			'tags'				=> 'array',
			'tags.*'			=> 'integer|exists:tags,id',
		]);

		$post = new Post;
		$post->title 			= $request->title;
		$post->slug 			= $request->slug;
		$post->category_id 		= $request->category_id;
		$post->body 			= Purifier::clean($request->body);
		$post->status			= $request->status;

		$post->excerpt = $request->excerpt ? $request->excerpt : $request->body; 
		$post->excerpt = Purifier::clean(strip_tags($post->excerpt));
		$post->excerpt = strlen($post->excerpt)<=253 ? $post->excerpt : substr($post->excerpt, 0, 256) . '...';  
		
		$post->author_id = $request->author_id ? $request->author_id : auth()->user()->id;

		if ($post->status == '4') { $post->published_at = date('Y-m-d H:i:s'); }

		if ($request->hasFile('featured_image')) {
			$image = $request->file('featured_image');
			$filename = time() . '.' . $image->getClientOriginalExtension();
			$location = public_path('images\\' . $filename);
			Image::make($image)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
			$post->image = $filename;
		}

		$myrc = $post->save();

		if ($myrc) {
			$myrc = $post->tags()->sync($request->tags, false);
			Session::flash('success', 'Post "' . $post->slug . '" was successfully saved.');
			return redirect()->route('posts.show', $post->id);
		} else {
			Session::flash('failure', 'Post "' . $post->slug . '" was NOT saved.');
            return redirect()->route('posts.create', $id)->withInput();
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function search(Request $request) {
	dd($request);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		$post = Post::findOrFail($id);
        $post->author_name = User::select('name')->where('id', $post->author_id)->get()->pluck('name')[0]; 	
        $post->category_name = Category::select('name')->where('id', $post->category_id)->get()->pluck('name')[0]; 	
        $post->status_name = ['Dead', 'Witheld', 'In Draft', 'Under Review', 'Published', 'Test'][$post->status]; 	

		if ($post) {
            return view('manage.posts.show', ['post' => $post]);
		} else {
			Session::flash('failure', 'Post "' . $id . '" was NOT found.');
            return Redirect::back();
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		$post = Post::findOrFail($id);

		$categories = Category::orderBy('name', 'asc')->pluck('name', 'id');
		$tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');
		$users = User::orderBy('name', 'asc')->pluck('name', 'id');
        $post->author_name = User::select('name')->where('id', $post->author_id)->get()->pluck('name')[0]; 	
        $post->category_name = Category::select('name')->where('id', $post->category_id)->get()->pluck('name')[0]; 	
        $post->status_name = ['Dead', 'Witheld', 'In Draft', 'Under Review', 'Published', 'Test'][$post->status]; 	

	    if ($post) {
            return view('manage.posts.edit', ['post' => $post,
            		'categories' => $categories, 'tags' => $tags, 'users' => $users, 'status_list' => status()]);
        } else {
            Session::flash('failure', 'Post "' . $id . '" was NOT found.');
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
		$post = Post::findOrFail($id);

		$this->validate($request, [
			'title' 			=> 'required|min:8|max:191',
			'slug' 				=> 'required|alpha_dash|min:5|max:191|unique:posts,slug,' . $id,
			'category_id' 		=> 'required|integer|exists:categories,id',
			'featured_image' 	=> 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
			'body' 				=> 'required|min:1',
			'excerpt' 			=> 'sometimes',
			'author_id' 		=> 'sometimes|integer|exists:users,id',
			'status' 			=> 'required|integer|min:0|max:4',
			'tags'				=> 'array',
			'tags.*'			=> 'integer|exists:tags,id',
		]);
	
		$post->title 			= $request->title;
		$post->slug 			= $request->slug;
		$post->category_id 		= $request->category_id;
		$post->body 			= Purifier::clean($request->body);
		$post->status			= $request->status;

		$post->excerpt = $request->excerpt ? $request->excerpt : $request->body; 
		$post->excerpt = Purifier::clean(strip_tags($post->excerpt));
		$post->excerpt = strlen($post->excerpt)<=253 ? $post->excerpt : substr($post->excerpt, 0, 256) . '...';  
		
		$post->author_id = $request->author_id ? $request->author_id : auth()->user()->id;

		if 		( $post->published_at && $post->status !== '4') { $post->published_at = null; }
		elseif 	(!$post->published_at && $post->status  == '4') { $post->published_at = date('Y-m-d H:i:s'); }

		if ($request->hasFile('featured_image')) {
			$oldFilename = $post->image;

			$image = $request->file('featured_image');
			$filename = time() . '.' . $image->getClientOriginalExtension();
			$location = public_path('images\\' . $filename);
			$myrc = Image::make($image)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
			$post->image = $filename;
			$msgOK = 'Post "' . $post->slug . '" and its image file "' . $image->getClientOriginalName() . '" were successfully saved.';
		} elseif ($request->delete_image) {
			$oldFilename = $post->image;
			$post->image = null;
			$msgOK = 'Post "' . $post->slug . '" was successfully saved and its image file "' . $oldFilename . '" deleted.';
		} else {
			$oldFilename = false;
			$msgOK = 'Post "' . $post->slug . '" was successfully saved.';
		}

		$myrc = $post->save();

		if ($myrc) {
			$myrc = $post->tags()->sync($request->tags, true);
			if ($oldFilename) {
				Storage::delete($oldFilename);
			}
			Session::flash('success', $msgOK);
			return redirect()->route('posts.show', $post->id);
		} else {
			Session::flash('failure', 'Post "' . $post->slug . '" was NOT saved.');
            return redirect()->route('posts.edit', $id)->withInput();
		}
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id) {
        $post = Post::findOrFail($id);
        $post->author_name = User::select('name')->where('id', $post->author_id)->get()->pluck('name')[0]; 	
        $post->category_name = Category::select('name')->where('id', $post->category_id)->get()->pluck('name')[0]; 	
        $post->status_name = ['Dead', 'Witheld', 'In Draft', 'Under Review', 'Published', 'Test'][$post->status]; 	

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
		$post = Post::findOrFail($id);

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
		return redirect()->route('posts.index');
	}

	/**
	 * API Check unique
	 * We check if exists & modify it if it does. 
	 */
	public function apiCheckUnique(Request $request) {
		$slug = $request->slug;
	
		for ($i=1; $i<100; $i++) {
			if (!Post::where('slug', '=', $slug)->exists()) {
				break;
			} else {
				if ($request->id) {
					$post = Post::find($id);
					if ($post->slug == $slug) {
						break;
					}	
				}	
				$slug = $request->slug . '-' . $i;
			} 
		}
		return json_encode($slug);
	}

}
