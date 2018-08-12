<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Tag;
use Session;

class TagController extends Controller
{

    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            session(['zone' => 'Tags']);
            return $next($request);
        });
    }

    // This Query Builder searches our table/columns and related_tables/columns for each word/phrase.
    // It requires the custom search_helper() function in Helpers.php.
    // If you change Helpers.php you should do "dump-autoload". 
    public function searchQuery($search = '') {
        $query = [
            'model'         => 'Tag',
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
        $tags = $this->searchQuery($request->search)->orderBy('name', 'asc')->paginate(10);
        if ($tags && $tags->count() > 0) {

        } else {
            Session::flash('failure', 'No Tags were found.');
        }
        return view('manage.tags.index', ['tags' => $tags, 'search' => $request->search]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|min:3|max:191|unique:tags,name',
        ]);

        $tag = new Tag;
        $tag->name = $request->name;
        $myrc = $tag->save();

        if ($myrc) {
            Session::flash('success', 'Tag "' . $tag->name . '" was successfully saved.');
            return redirect()->route('tags.index');
        } else {
            Session::flash('failure', 'Tag "' . $request->name . '" was NOT saved.');
            return redirect()->route('tags.index')->withInput();
        }
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $zone = '*') {
        $tag=Tag::findOrFail($id);

        $albums = false;
        $files  = false;
        $photos = false;
        $posts  = false;
        $list   = false;
        if ($zone == 'Albums' or $zone == 'Photos' or $zone == '*' or $zone == 'Tags') {
            $albums = $tag->albums()->orderBy('id', 'desc')->paginate(5, ['*'], 'pageA');
        }
        if ($zone == 'Files' or $zone == '*' or $zone == 'Tags') {
            $list['d'] = folderStatus();
            $list['f'] = fileStatus();
            $files  = $tag->files() ->orderBy('id', 'desc')->paginate(5, ['*'], 'pageFi');
        }
        if ($zone == 'Photos' or $zone == '*' or $zone == 'Tags') {
            $photos = $tag->photos()->orderBy('id', 'desc')->paginate(5, ['*'], 'pageI');
        }
        if ($zone == 'Posts'  or $zone == '*' or $zone == 'Tags') {
            $list['p'] = postStatus();
            $posts  = $tag->posts() ->orderBy('id', 'desc')->paginate(5, ['*'], 'pageP');
        }

        if ($tag) {
            return view('manage.tags.show', ['tag' => $tag, 'albums' => $albums, 'files' => $files, 'photos' => $photos,
                'posts' => $posts, 'list' => $list]);
        } else {
            Session::flash('failure', 'Tag "' . $id . '" was NOT found.');
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
        return redirect()->route('tags.index',['edit'=>$id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $tag = Tag::findOrFail($id);

        $this->validate($request, [
            'name' => 'required|min:3|max:191|unique:tags,name,' . $id,
        ]);

        $tag->name = $request->name;
        $myrc = $tag->save();

        if ($myrc) {
            Session::flash('success', 'Tag "' . $tag->name . '" was successfully saved.');
        } else {
            Session::flash('failure', 'Tag "' . $request->name . '" was NOT saved.');
        }
        return redirect()->route('tags.index', ['page' => $request->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id) {
        $tag = Tag::findOrFail($id);

       if ($tag) {
            return view('manage.tags.delete', ['tag' => $tag]);
        } else {
            Session::flash('failure', 'Tag "' . $id . '" was NOT found.');
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
        $tag = Tag::findOrFail($id);
        if ($tag){
            $myrc = $tag->delete();
            Session::flash('success', 'The "' . $tag->name . '" Tag was successfully deleted.');
            return Redirect::to($request->url);            
        } else {
            Session::flash('failure', 'Tag "' . $id . '"was NOT deleted.');
            return Redirect::back();            
        }        
    }

}
