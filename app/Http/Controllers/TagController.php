<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Tag;
use Session;
use Validator;

class TagController extends Controller
{

    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            $owner_id = $request->route('user') ?: '*';         

            if (!permit($this->permits($owner_id))) {
                Session::flash('failure', "It doesn't look like you have permission for that action!");
                return redirect(previous());
            }

            session(['zone' => 'Tags']);                        // Set the active zone for search()
            previous(url($request->getPathInfo()));             // Set the previous url for redirect(previous()) 
            return $next($request);
        });
    }

    // These permits are used by permit() in the __contruct() middleware to secure the controller actions 
    // This could be done in the Route config - but it seems to make more sense to do it in the controller.
    // $owner_id='*' permits all Users for a permission   
    public function permits($owner_id='^') {
        $permits = [
            'index'   => 'users:*,permission:tags-read',
            'show'    => 'users:*,permission:tags-read',
            'create'  => 'permission:tags-create',
            'store'   => 'permission:tags-create',
            'edit'    => 'permission:tags-update',
            'update'  => 'permission:tags-update',
            'delete'  => 'permission:tags-delete',
            'destroy' => 'permission:tags-delete',
            'default' => '' 
        ];
        return $permits;
    }

    // This Query Builder searches our table/columns and related_tables/columns for each word/phrase.
    // The table is sorted (ascending or descending) and finally filtered.
    // It requires the custom queryHelper() function in Helpers.php.
    public function query() {
        $query = [
            'model'         => 'Tag',
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
           return redirect()->route('tags.index');
        } 
        $tags = paginateHelper($this->query(), $request, 12, 4, 192, 4); // size($request->pp), default, min, max, step

        if ($tags && $tags->count() > 0) {

        } else {
            Session::flash('failure', 'No Tags were found.');
        }
        return view('manage.tags.index', ['tags' => $tags, 'search' => $request->search, 'sort' => $request->sort]);
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
            'name' => 'required|min:3|max:191|unique:tags,name',
        ]);
        if ($validator->fails()) {
            return redirect()->route('tags.index')->withErrors($validator)->withInput();
        }

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
        $tag=Tag::find($id);

        if ($tag) {
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
            return view('manage.tags.show', ['tag' => $tag, 'albums' => $albums, 'files' => $files, 'photos' => $photos,
                'posts' => $posts, 'list' => $list]);
        } else {
            Session::flash('failure', 'Tag "' . $id . '" was NOT found.');
            return redirect(previous());
            return redirect()->route('tags.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        return redirect()->route('tags.index', ['edit' => $id]);
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

        // We use our own validator here as auto validation cannot handle error return from a POST method 
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:191|unique:tags,name,' . $id,
        ]);
        if ($validator->fails()) {
            return redirect()->route('tags.index', ['edit' => $id])->withErrors($validator)->withInput();
        }

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
        $tag = Tag::find($id);

        if ($tag) {
            return view('manage.tags.delete', ['tag' => $tag]);
        } else {
            Session::flash('failure', 'Tag "' . $id . '" was NOT found.');
            return redirect(previous());
            return redirect()->route('tags.index');
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
 
        $myrc = $tag->delete();
        
        if ($myrc){
            Session::flash('success', 'The "' . $tag->name . '" Tag was successfully deleted.');
            return redirect()->route('tags.index');            
        } else {
            Session::flash('failure', 'Tag "' . $id . '"was NOT deleted.');
            return redirect()->route('tags.delete', $id)->withinput();
        }        
    }

}
