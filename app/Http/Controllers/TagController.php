<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tag;
use Session;

class TagController extends Controller
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
       $tags = Tag::orderBy('id', 'desc')->paginate(10);

        if ($tags) {

        } else {
            Session::flash('failure', 'No Tags were found.');
        }
        return view('tags.index', ['tags' => $tags]);
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
            'name' => 'required|min:3|max:191',
        ]);

        $tag = new Tag;
        $tag->name = $request->name;
        $myrc = $tag->save();

        if ($myrc) {
            Session::flash('success', 'The Tag was successfully saved.');
        } else {
            Session::flash('failure', 'The Tag was NOT saved.');
        }
        return redirect()->route('tags.index');
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $tag=Tag::find($id);

        if ($tag) {

        } else {
            Session::flash('failure', 'Tag ' . $id . ' was NOT found.');
        }
        return view('tags.show', ['tag' => $tag]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->route('tags.index',['edit'=>$id]);
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
        $tag = Tag::find($id);

        $this->validate($request, [
            'name' => 'required|min:3|max:191',
        ]);

        $tag->name = $request->name;
        $myrc = $tag->save();

        if ($myrc) {
            Session::flash('success', 'The Tag was successfully saved.');
        } else {
            Session::flash('failure', 'The Tag was NOT saved.');
        }
        return redirect()->route('tags.index',['page'=>$request->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $myrc=false ;   

        if ($myrc) {
            Session::flash('success', 'The Tag was deleted.');
        } else {
            Session::flash('failure', 'Delete Tag is not yet supported!');
        }        
        return redirect()->route('tags.index',['page'=>$request->page]);
    }
}
