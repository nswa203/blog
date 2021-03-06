<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Post;
use App\Album;
use App\Category;
use App\Folder;
use App\Tag;
use App\User;
use Auth;
use Image;
use Purifier;
use Session;
use Storage;
use Validator;

use Route;

class PostController extends Controller
 {

    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            $post = $request->route('post') ?: false;
            if ($post) {
                $owner_id = Post::find($post)->author_id;
            } else {
                $owner_id = $request->author_id ?: false;
            } 
            $owner_id = $owner_id ? $owner_id : '*'; 

            if (!permit($this->permits($owner_id))) {
                Session::flash('failure', "It doesn't look like you have permission for that action!");
                return redirect(previous());
            }

            // This codes extends validation to ensure restricted users may only access
            // their own User & Folder resources   
            if (permit($this->permits(), 'updateAll')) { 
                Validator::extend('ownFolder', function() { return true; });
                Validator::extend('ownUser'  , function() { return true; });
            } else {
                Validator::extend('ownFolder', function() use ($request) {
                    return areOwnedBy($request->folders, 'Folder', 'user_id', auth()->user()->id); },
                    "Invalid Folder."
                );
                Validator::extend('ownUser', function() use ($request, $owner_id) {
                    return $owner_id==auth()->user()->id; },
                    "Invalid User."
                );
            } 

            session(['zone' => 'Posts']);                       // Set the active zone for search()
            previous(url($request->getPathInfo()));             // Set the previous url for redirect(previous()) 
            return $next($request);
        });
    }

    // These permits are used by permit() in the __contruct() middleware to secure the controller actions 
    // This could be done in the Route config - but it seems to make more sense to do it in the controller.
    // $owner_id='*' permits all Users for a permission   
    public function permits($owner_id='^') {
        $permits = [
            'showAll'   	 => 'permission:posts-read',
            'index'     	 => 'permission:posts-read,owner:'.$owner_id.'|posts-read-ifowner',
            'show'      	 => 'permission:posts-read,owner:'.$owner_id.'|posts-read-ifowner',
            'create'    	 => 'permission:posts-create|posts-create-ifowner',
            'store'     	 => 'permission:posts-create|posts-create-ifowner',
            'edit'      	 => 'permission:posts-update,owner:'.$owner_id.'|posts-update-ifowner',
            'updateAll' 	 => 'permission:posts-update',
            'update'    	 => 'permission:posts-update,owner:'.$owner_id.'|posts-update-ifowner',
            'delete'    	 => 'permission:posts-delete,owner:'.$owner_id.'|posts-delete-ifowner',
            'destroy'   	 => 'permission:posts-delete,owner:'.$owner_id.'|posts-delete-ifowner',
            'apiCheckUnique' => 'user:*',
            'default'   	 => '' 
        ];
        return $permits;
    }

    // This Query Builder searches our table/columns and related_tables/columns for each word/phrase.
    // The table is sorted (ascending or descending) and finally filtered.
    // Looses searches are performed unless the search string is enclosed in '^'
    // It requires the custom queryHelper() function in Helpers.php.
    // $posts = paginateHelper($this->query(), $request, 12, 4, 192, 4);
    public function query($user_id=false) {
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
            'sort' 		  => [
                'i'       => 'd,id',                                                      
                't'       => 'a,title',
                'e'       => 'a,excerpt',
                'p'       => 'd,published_at',
                'g'	  	  => 'a,name,category', 		// Associated search
                'a'		  => 'a,name,user',				// Associated search
                'default' => 'i'                       
            ],
        ];
        if ($user_id) {
            $query['filter'] = ['author_id', '=', $user_id];
        }
        return $query;
    }

    // $status_list
	public function status($default = -1) {
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

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
        //$posts = paginateHelper($this->query(), $request, 12, 4, 192, 4); // size($request->pp), default, min, max, step
        $id = permit($this->permits(), 'showAll') ? '' : Auth::user()->id;
        $posts = paginateHelper($this->query($id), $request, 12, 4, 192, 4);  

		if ($posts && $posts->count() > 0) {

		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('manage.posts.index', ['posts' => $posts,
			'search' => $request->search, 'sort' => $request->sort, 'status_list' => $this->status()]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		$post = new Post;

        if (permit($this->permits(), 'updateAll')) {
            $folders = Folder::orderBy('name', 'asc')->pluck('name', 'id');
            $users   =   User::orderBy('name', 'asc')->pluck('name', 'id');            
        } else {
            $folders = Folder::where('user_id', Auth::user()->id)->orderBy('name', 'asc')->pluck('name', 'id');
            $users   = [Auth::user()->id => Auth::user()->name];            
        }

		$categories = Category::orderBy('name', 'asc')->pluck('name', 'id');
		$tags       = Tag::     orderBy('name', 'asc')->pluck('name', 'id');

		return view('manage.posts.create', ['post' => $post,
			'categories' => $categories, 'folders' => $folders, 'tags' => $tags, 'users' => $users, 'status_list' => $this->status(2)]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
        // We use our own validator here as auto validation cannot handle error return from a POST method 
        $validator = Validator::make($request->all(), [
			'title' 	  => 'required|min:8|max:191',
			'slug' 		  => 'required|alpha_dash|min:5|max:191|unique:posts,slug',
			'category_id' => 'required|integer|exists:categories,id',
			'image'	 	  => 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
			'banner'	  => 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
			'body' 		  => 'required|min:1',
			'excerpt' 	  => 'sometimes',
			'author_id'   => 'sometimes|integer|exists:users,id|ownUser',
			'status' 	  => 'required|integer|min:0|max:4',
			'folders'	  => 'array|ownFolder',
			'folders.*'	  => 'integer|exists:folders,id',			
			'tags'		  => 'array',
			'tags.*'	  => 'integer|exists:tags,id',
        ]);
        if ($validator->fails()) {
            return redirect()->route('posts.create')->withErrors($validator)->withInput();
        }

		$post = new Post;

		$post->title 	   = $request->title;
		$post->slug 	   = $request->slug;
		$post->category_id = $request->category_id;
		$post->body 	   = Purifier::clean($request->body);
		$post->status	   = $request->status;

		$post->excerpt = $request->excerpt ? $request->excerpt : $request->body; 
		$post->excerpt = Purifier::clean(strip_tags($post->excerpt));
		$post->excerpt = strlen($post->excerpt)<=253 ? $post->excerpt : substr($post->excerpt, 0, 256) . '...';  
		
		$post->author_id = $request->author_id ? $request->author_id : auth()->user()->id;

		if ($post->status == '4') { $post->published_at = date('Y-m-d H:i:s'); }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = microtime() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images\\' . $filename);
            //Image::make($image)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
            $myrc = Image::make($image)->widen(800, function ($constraint) { $constraint->upsize(); })->save($location);
            $post->image = $filename;
            $msgs[] = 'Image "' . $image->getClientOriginalName() . '" was successfully saved as ' . $filename;
        }
        if ($request->hasFile('banner')) {
            $image = $request->file('banner');
            $filename = microtime() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images\\' . $filename);
            //Image::make($image)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
            $myrc = Image::make($image)->widen(800, function ($constraint) { $constraint->upsize(); })->save($location);
            $post->banner = $filename;
            $msgs[] = 'Image "' . $image->getClientOriginalName() . '" was successfully saved as ' . $filename;
        }

		$myrc = $post->save();
        
        if (isset($msgs)) { session()->flash('msgs', $msgs); }

		if ($myrc) {
			// Posts inherit all tags from the files in any attached folders
			if (!$request->tags) { $request->tags = [];}
			if ($request->folders) {
				foreach($request->folders as $item) {
					$folder = Folder::find($item);
					if ($folder) {
						foreach ($folder->files as $file) {
							$request->tags = array_merge($request->tags, $file->tags->pluck('id')->toArray());
						}
					}
				}
			}	
			$request->tags = array_unique($request->tags);
	
			$myrc = $post->folders()->sync($request->folders, false);
			$myrc = $post->tags()   ->sync($request->tags,    false);

			Session::flash('success', 'Post "' . $post->slug . '" was successfully saved.');
			return redirect()->route('posts.show', $post->id);
		} else {
			Session::flash('failure', 'New Post was NOT saved.');
            return redirect()->route('posts.create')->withInput();
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id) {
		$post = Post::find($id);

		if ($post) {
	        $comments = $post->comments()->orderBy('id', 'desc')->paginate(5, ['*'], 'pageC');
	        $folders  = $post->folders() ->orderBy('slug','asc')->paginate(5, ['*'], 'pageF');
            return view('manage.posts.show', ['post' => $post, 'comments' => $comments, 'folders' => $folders, 'status_list' => $this->status()]);
		} else {
			Session::flash('failure', 'Post "' . $id . '" was NOT found.');
            return redirect(previous());
            return redirect()->route('posts.index');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		$post = Post::find($id);

        if ($post) {
            if (permit($this->permits(), 'updateAll')) {
                $folders = Folder::orderBy('name', 'asc')->pluck('name', 'id');
                $users   =   User::orderBy('name', 'asc')->pluck('name', 'id');            
            } else {
                $folders = Folder::where('user_id', Auth::user()->id)->orderBy('name', 'asc')->pluck('name', 'id');
                $users   = [Auth::user()->id => Auth::user()->name];            
            }

			$categories = Category::orderBy('name', 'asc')->pluck('name', 'id');
			$tags       = Tag::     orderBy('name', 'asc')->pluck('name', 'id');

            return view('manage.posts.edit', ['post' => $post, 'categories' => $categories,
            	'folders' => $folders, 'tags' => $tags, 'users' => $users, 'status_list' => $this->status()]);
        } else {
            Session::flash('failure', 'Post "' . $id . '" was NOT found.');
            return redirect(previous());
			return redirect()->route('posts.index');
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
        // We use our own validator here as auto validation cannot handle error return from a POST method 
        $validator = Validator::make($request->all(), [
			'title' 			=> 'required|min:8|max:191',
			'slug' 				=> 'required|alpha_dash|min:5|max:191|unique:posts,slug,' . $id,
			'category_id' 		=> 'required|integer|exists:categories,id',
			'image'			 	=> 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
			'banner'	 		=> 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
			'body' 				=> 'required|min:1',
			'excerpt' 			=> 'sometimes',
			'author_id' 		=> 'sometimes|integer|exists:users,id|ownUser',
			'status' 			=> 'required|integer|min:0|max:4',
			'folders'			=> 'array|ownFolder',
			'folders.*'			=> 'integer|exists:folders,id',			
			'tags'				=> 'array',
			'tags.*'			=> 'integer|exists:tags,id',
        ]);
        if ($validator->fails()) {
            return redirect()->route('posts.edit')->withErrors($validator)->withInput();
        }

		$post = Post::findOrFail($id);

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

        if ($request->hasFile('image')) {
            $oldFiles[]=$post->image;
            $image = $request->file('image');
            $filename = microtime() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images\\' . $filename);
            //Image::make($image)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
            $myrc = Image::make($image)->widen(800, function ($constraint) { $constraint->upsize(); })->save($location);
            $post->image = $filename;
            $msgs[] = 'Image "' . $image->getClientOriginalName() . '" was successfully saved as ' . $filename;
        } elseif ($request->delete_image) {
            $oldFiles[] = $post->image;
            $msgs[] = 'Image "' . $post->image . '" deleted.';
            $post->image = null;
        } else {
            //$msgs[] = 'Image "' . $post->image . '" was successfully saved.';
        }

        if ($request->hasFile('banner')) {
            $oldFiles[] = $post->banner;
            $image = $request->file('banner');
            $filename = microtime() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images\\' . $filename);
            //Image::make($banner)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
            $myrc = Image::make($image)->widen(800, function ($constraint) { $constraint->upsize(); })->save($location);
            $post->banner = $filename;
            $msgs[] = 'Image "' . $image->getClientOriginalName() . '" was successfully saved as ' . $filename;
        } elseif ($request->delete_banner) {
            $oldFiles[] = $post->banner;
            $msgs[] = 'Image "' . $post->banner . '" deleted.';
            $post->banner = null;
        } else {
            //$msgs[] = 'Image "' . $post->banner . '" was successfully saved.';
        }

		$myrc = $post->save();

        if (isset($msgs)) { session()->flash('msgs', $msgs); }

		if ($myrc) {
			// Posts inherit all tags from the files in any attached folders
			if (!$request->tags) { $request->tags = [];}
			if ($request->folders) {
				foreach($request->folders as $item) {
					$folder = Folder::find($item);
					if ($folder) {
						foreach ($folder->files as $file) {
							$request->tags = array_merge($request->tags, $file->tags->pluck('id')->toArray());
						}
					}
				}
			}	
			$request->tags = array_unique($request->tags);

			$myrc = $post->folders()->sync($request->folders, true);
			$myrc = $post->tags()   ->sync($request->tags,    true);
            if (isset($oldFiles)) { Storage::delete($oldFiles); }
            Session::flash('success', 'Post "' . $post->slug . '" was successfully saved.');
            return redirect()->route('posts.show', $id);
		} else {
			Session::flash('failure', 'Post "' . $id . '" was NOT saved.');
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
        $post = Post::find($id);

        if ($post) {
	        return view('manage.posts.delete', ['post'=>$post, 'status_list' => $this->status()]);
        } else {
            Session::flash('failure', 'Post ' . $id . ' was NOT found.');
            return redirect(previous());
            return redirect()->route('posts.index');
        }
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		$post = Post::findOrFail($id);

		$myrc = $post->delete();
		
		if ($myrc) {
			if ($post->image) {
				Storage::delete($post->image);
                $msgs[] = 'Image "' . $post->image . '" was successfully deleted.';
			}
			if ($post->banner) {
				Storage::delete($post->banner);
                $msgs[] = 'Image "' . $post->banner . '" was successfully deleted.';
			}
			Session::flash('success', 'Blog Post ' . $post->slug . ' was successfully deleted.');
            if (isset($msgs)) { session()->flash('msgs', $msgs); }
			return redirect()->route('posts.index');
        } else {
            Session::flash('failure', 'Post ' . $id . ' was NOT Deleted.');
            return redirect()->route('posts.delete', $id)->withInput();
        }
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
