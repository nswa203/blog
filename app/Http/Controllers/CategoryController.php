<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Category;
use File;
use Session;
use Validator;

class CategoryController extends Controller
{

    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            $owner_id = $request->route('user') ?: '*';         

            if (!permit($this->permits($owner_id))) {
                Session::flash('failure', "It doesn't look like you have permission for that action!");
                return redirect(previous());
            }

            session(['zone' => 'Categories']);                  // Set the active zone for search()
            previous(url($request->getPathInfo()));             // Set the previous url for redirect(previous()) 
            return $next($request);
        });
    }

    // These permits are used by permit() in the __contruct() middleware to secure the controller actions 
    // This could be done in the Route config - but it seems to make more sense to do it in the controller.
    // $owner_id='*' permits all Users for a permission   
    public function permits($owner_id='^') {
        $permits = [
            'index'   => 'users:*,permission:categories-read',
            'show'    => 'users:*,permission:categories-read',
            'create'  => 'permission:categories-create',
            'store'   => 'permission:categories-create',
            'edit'    => 'permission:categories-update',
            'update'  => 'permission:categories-update',
            'delete'  => 'permission:categories-delete',
            'destroy' => 'permission:categories-delete',
            'default' => '' 
        ];
        return $permits;
    }

    // This Query Builder searches our table/columns and related_tables/columns for each word/phrase.
    // The table is sorted (ascending or descending) and finally filtered.
    // It requires the custom queryHelper() function in Helpers.php.
    public function query() {
        $query = [
            'model'         => 'Category',
            'searchModel'   => ['name'],
            'searchRelated' => [],
            'sort'   => [
                'i'       => 'd,id',                                                      
                'n'       => 'a,name',
                'c'       => 'd,created_at',
                'u'       => 'd,updated_at',
                'default' => 'n'                       
            ],                        
        ];
        return $query;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->edit && !permit($this->permits(), 'edit')) {
           Session::flash('failure', "It doesn't look like you have permission for that action!");
           return redirect()->route('categories.index');
        } 
        $categories = paginateHelper($this->query(), $request, 12, 4, 192, 4); // size($request->pp), default, min, max, step

        if ($categories && $categories->count()>0) {

        } else {
            Session::flash('failure', 'No Categories were found.');
        }
        return view('manage.categories.index', ['categories' => $categories, 'search' => $request->search, 'sort' => $request->sort]);
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
            'name' => 'required|min:5|max:191|unique:categories,name',
        ]);
        if ($validator->fails()) {
            return redirect()->route('categories.index')->withErrors($validator)->withInput();
        }

        $category = new Category;
        $category->name = $request->name;
        $myrc = $category->save();

        if ($myrc) {
            Session::flash('success', 'Category "' . $category->name . '" was successfully saved.');
            return redirect()->route('categories.index');
        } else {
            Session::flash('failure', 'Category "' . $request->name . '" was NOT saved.');
            return redirect()->route('categories.index')->withInput();
        }
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $zone = '*') {
        $category = Category::find($id);

        if ($category) {
            $albums  = false;
            $folders = false;
            $posts   = false;
            
            if ($zone == 'Albums' or $zone == 'Photos' or $zone == '*' or $zone == 'Categories') {
                $albums = $category->albums()->orderBy('id', 'desc')->paginate(5, ['*'], 'pageA');
            }
            if ($zone == 'Folders' or $zone == 'Files' or $zone == '*' or $zone == 'Categories') {
                $list['d'] = folderStatus();
                $folders  = $category->folders()->orderBy('id', 'desc')->paginate(5, ['*'], 'pageF');
            }
            if ($zone == 'Posts'  or $zone == '*' or $zone == 'Categories') {
                $list['p'] = postStatus();
                $posts  = $category->posts()->orderBy('id', 'desc')->paginate(5, ['*'], 'pageP');
            }
            return view('manage.categories.show', ['category' => $category, 'albums' => $albums, 'folders'  => $folders,
                'posts' => $posts, 'list' => $list]);
        } else {
            Session::flash('failure', 'Category "' . $id . '" was NOT found.');
            return redirect(previous());
            return redirect()->route('categories.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        return redirect()->route('categories.index', ['edit' => $id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $category = Category::findOrFail($id);

        // We use our own validator here as auto validation cannot handle error return from a POST method 
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5|max:191|unique:categories,name,' . $id,
        ]);
        if ($validator->fails()) {
            return redirect()->route('categories.index', ['edit' => $id])->withErrors($validator)->withInput();
        }

        $category->name = $request->name;
        $myrc = $category->save();

        if ($myrc) {
            Session::flash('success', 'Category "' . $category->name . '" was successfully saved.');
        } else {
            Session::flash('failure', 'Category "' . $request->name . '" was NOT saved.');
        }
        return redirect()->route('categories.index', ['page' => $request->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id) {
        $category = Category::find($id);

        if ($category) {
            foreach ($category->albums as $album) {
                $msg = 'Album "' . $album->title . '" will also be deleted!';
                msgx(['Dependencies' => [$msg, true]]);                    
            }
            foreach ($category->folders as $folder) {
                $msg = 'Folder "' . $folder->name . '" will also be deleted!';
                msgx(['Dependencies' => [$msg, true]]);            }
            foreach ($category->posts as $post) {
                $msg = 'Post "' . $post->title . '" will also be deleted!';
                msgx(['Dependencies' => [$msg, true]]);                    
            }
            return view('manage.categories.delete', ['category' => $category]);
        } else {
            Session::flash('failure', 'Category "' . $id . '" was NOT found.');
            return redirect(previous());
            return redirect()->route('categories.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        $category = Category::findOrFail($id);

        foreach ($category->albums as $album) {
            $myrc = true;
            $msg = 'Album "' . $album->title . '" was successfully deleted';
            msgx(['Dependencies' => [$msg, $myrc!=null]]);                    
        }
        foreach ($category->folders as $folder) {
            if ($folder->status == 1) {
                $msg = 'Public';
                $path = public_path($folder->directory);
            } else {
                $msg = 'Private';
                $path = private_path($folder->directory);
            }
            $myrc = File::deleteDirectory($path);
            if ($myrc) {
                $msg = $msg . ' Directory "' . $folder->directory . '" was successfully deleted.';
                msgx(['Dependencies' => [$msg, true]]);
            } else {
                $msg = $msg . ' Directory "' . $folder->directory . '" could NOT be deleted.';
                msgx(['failure' => [$msg, true]]);                    
            }    
        }
        foreach ($category->posts as $post) {
            if ($post->banner) {
                $path = public_path('images\\' . $post->banner);
                $myrc = File::delete($path);
                $msg = 'Post Banner "' . $path . '" was successfully deleted.';
                msgx(['Dependencies' => [$msg, $myrc]]);
            }
            if ($post->image) {
                $path = public_path('images\\' . $post->image);
                $myrc = File::delete($path);
                $msg = 'Post Image "' . $path . '" was successfully deleted.';
                msgx(['Dependencies' => [$msg, $myrc]]);
            }      
        }

        $myrc = $category->delete();     // Migration Cascades will handle all DB dependencies 

        if ($myrc) { 
            Session::flash('success', 'Category "' . $category->name . '" deleted OK.');
            return redirect()->route('categories.index'); 
        } else {
            Session::flash('failure', 'Category "' . $id . '" was NOT deleted.');
            return redirect()->route('categories.delete', $id)->withinput();
        }
    }

}
