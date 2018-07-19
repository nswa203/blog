<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Category;
use Session;

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

        $albums = false;
        $posts  = false;
        if ($zone == 'Albums' or $zone == 'Photos' or $zone == '*' ) { $albums = $category->albums()->orderBy('id', 'desc')->paginate(5, ['*'], 'pageA'); }
        if ($zone == 'Posts'  or $zone == '*' ) { $posts  = $category->posts() ->orderBy('id', 'desc')->paginate(5, ['*'], 'pageP');  }

        if ($category) {
            return view('manage.categories.show', ['category' => $category, 'albums' => $albums, 'posts' => $posts]);
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
        // Can't yet delete Categories because
        // 1. blades don't support null Category & No default Category set up
        // 2. Don't know how to set all posts category-id fields to null easily 

        if ($category) {
            Session::flash('failure', 'Category "' . $id . '" was NOT DELETED.<br>Delete Not yet supported!');
            return redirect()->route('categories.index');
        } else {
            Session::flash('failure', 'Category "' . $id . '" was NOT deleted.');
            return Redirect::back();            
        }
    }

}
