<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Category;
use Session;
use File;

class CategoryController extends Controller
{

    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            session(['zone' => 'Categories']);
            return $next($request);
        });
    }

    // This Query Builder searches our table/columns and related_tables/columns for each word/phrase.
    // It requires the custom search_helper() function in Helpers.php.
    // If you change Helpers.php you should do "dump-autoload". 
    public function searchQuery($search = '') {
        $query = [
            'model'         => 'Category',
            'searchModel'   => ['name'],
            'searchRelated' => []
        ];
        return search_helper($search, $query);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $categories = $this->searchQuery($request->search)->orderBy('name', 'asc')->paginate(10);
        if ($categories) {

        } else {
            Session::flash('failure', 'No Categories were found.');
        }
        return view('manage.categories.index', ['categories' => $categories, 'search' => $request->search]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|min:5|max:191|unique:categories,name',
        ]);

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
        $category = Category::findOrFail($id);

        $albums  = false;
        $folders = false;
        $posts   = false;
        if ($zone == 'Albums' or $zone == 'Photos' or $zone == '*' ) {
            $albums = $category->albums()->orderBy('id', 'desc')->paginate(5, ['*'], 'pageA');
        }
        if ($zone == 'Folders' or $zone == 'Files' or $zone == '*' ) {
            $folders  = $category->folders()->orderBy('id', 'desc')->paginate(5, ['*'], 'pageF');
        }
        if ($zone == 'Posts'  or $zone == '*' ) {
            $posts  = $category->posts()->orderBy('id', 'desc')->paginate(5, ['*'], 'pageP');
        }

        if ($category) {
            return view('manage.categories.show', ['category' => $category, 'albums' => $albums, 'folders'  => $folders, 'posts' => $posts]);
        } else {
            Session::flash('failure', 'Category "' . $id . '" was NOT found.');
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
        $category = Category::find($id);

        $this->validate($request, [
            'name' => 'required|min:5|max:191|unique:categories,name,' . $id,
        ]);

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
        $category = Category::findOrFail($id);

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
            return Redirect::back();            
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

        if ($category) {
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
            Session::flash('success', 'Category "' . $category->name . '" deleted OK.');
            return Redirect::to($request->url);            
        } else {
            Session::flash('failure', 'Category "' . $id . '" was NOT found.');
            return Redirect::back();            
        }
    }

}
