<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Photo;
use App\Album;
use App\Tag;
use Session;
use Purifier;
use Image; 
use Storage;

class PhotoController extends Controller
{

    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            session(['zone' => 'Photos']);
            return $next($request);
        });
    }

    public function searchQuery($search = '') {
        $searchable1 = ['title', 'description', 'image'];
        $searchable2 = ['tags' => ['name'], 'albums' => ['title', 'slug', 'description']];
        $query = Photo::select('*')->with('album');

        if ($search !== '') {
            foreach ($searchable1 as $column) {
                $query->orWhere($column, 'LIKE', '%' . $search . '%');
            }
            foreach ($searchable2 as $table => $columns) {
                foreach ($columns as $column) {
                    $query->orWhereHas($table, function($q) use ($column, $search){
                        $q->where($column, 'LIKE', '%' . $search . '%');
                    }); 
                }
            }
        }  
        return $query;
    }

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->search) {
            $photos = $this->searchQuery($request->search)->orderBy('id', 'desc')->paginate(10);
        } else {
            $photos = Photo::orderBy('id', 'desc')->with('albums')->paginate(10);
        }
            
        $photos->status_names = $this->status();    

        if ($photos) {

        } else {
            Session::flash('failure', 'No Photos were found.');
        }
        return view('manage.photos.index', ['photos' => $photos, 'search' => $request->search]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $albums = Album::orderBy('slug', 'asc')->pluck('slug', 'id');
        $tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');
        $photo = new Photo;

        return view('manage.photos.create', ['photo' => $photo,
            'albums' => $albums, 'tags' => $tags, 'status_list' => $this->status(2)]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'title'             => 'required|min:5|max:191',
            'image'             => 'required|image|mimes:jpeg,jpg,jpe,png,gif|max:10000|min:1',
            'description'       => 'sometimes|max:2048',
            'status'            => 'required|integer|min:0|max:4',
            'album_ids'         => 'required|array|min:1',
            'album_ids.*'       => 'required|integer|exists:albums,id',
            'tags'              => 'array',
            'tags.*'            => 'integer|exists:tags,id',
        ]);

        $photo = new Photo;
        $photo->title            = $request->title;
        $photo->description      = Purifier::clean($request->description);
        $photo->status           = $request->status;
        $photo->size             = '0';
        
        if ($photo->status == '4') { $photo->published_at = date('Y-m-d H:i:s'); }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = microtime() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images\\' . $filename);
            //$myrc = Image::make($image)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
            $myrc = Image::make($image)->widen(800, function ($constraint) { $constraint->upsize(); })->save($location);

            $photo->image = $filename;
            $msgs[] = 'Image "' . $image->getClientOriginalName() . '" was successfully saved as ' . $filename;
        }

        $myrc = $photo->save();

        if (isset($msgs)) { session()->flash('msgs', $msgs); }
        if ($myrc) {
            $myrc = $photo->tags()->sync($request->tags, false);
            $myrc = $photo->albums()->sync($request->album_ids, false);
            Session::flash('success', 'Album "' . $photo->slug . '" was successfully saved.');
            return redirect()->route('photos.show', $photo->id);
        } else {
            Session::flash('failure', 'Album "' . $id . '" was NOT saved.');
            return redirect()->route('photos.create', $id)->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $photo = Photo::findOrFail($id);
        $photo->status_name = $this->status()[$photo->status];    

        if ($photo) {
            return view('manage.photos.show', ['photo' => $photo]);
        } else Session::flash('failure', 'Photo "' . $id . '" was NOT found.');
            return Redirect::back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $photo = Photo::findOrFail($id);

        $tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');
        $albums = Album::orderBy('slug', 'asc')->pluck('slug', 'id');
        $photo->status_name = $this->status()[$photo->status];    

        if ($photo) {
            return view('manage.albums.edit', ['photo' => $photo,
                    'categories' => $categories, 'tags' => $tags, 'albums' => $albums, 'status_list' => $this->status()]);
        } else {
            Session::flash('failure', 'Photo "' . $id . '" was NOT found.');
            return Redirect::back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Photo $photo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Photo $photo)
    {
        //
    }
}
