<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use Session;

class CategoryController extends Controller
{
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
    public function index()
    {
        $categories = Category::orderBy('id', 'desc')->paginate(3);

        if ($categories) {

        } else {
            Session::flash('failure', 'No Categories were found.');
        }
        return view('categories.index', ['categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:5|max:191',
        ]);

        $category = new Category;
        $category->name = $request->name;
        $myrc = $category->save();

        if ($myrc) {
            Session::flash('success', 'The Category was successfully saved.');
        } else {
            Session::flash('failure', 'The Category was NOT saved.');
        }
        return redirect()->route('categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->route('categories.index',['edit'=>$id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        $this->validate($request, [
            'name' => 'required|min:5|max:191',
        ]);

        $category->name = $request->name;
        $myrc = $category->save();

        if ($myrc) {
            Session::flash('success', 'The Category was successfully saved.');
        } else {
            Session::flash('failure', 'The Category was NOT saved.');
        }
        return redirect()->route('categories.index',['page'=>$request->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $myrc=true ;   

        if ($myrc) {
            Session::flash('success', 'The Category was deleted.');
        } else {
            Session::flash('failure', 'The Category was NOT deleted.');
        }        
        return redirect()->route('categories.index',['page'=>$request->page]);
    }
}
