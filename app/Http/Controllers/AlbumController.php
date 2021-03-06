<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Album;
use App\Category;
use App\Photo;
use App\Post;
use App\Tag;
use App\User;
use Session;
use Purifier;
use Image; 
use Storage;

class AlbumController extends Controller
{

    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            session(['zone' => 'Albums']);
            return $next($request);
        });
    }

    // This Query Builder searches our table/columns and related_tables/columns for each word/phrase.
    // The table is sorted (ascending or descending) and finally filtered.
    // It requires the custom queryHelper() function in Helpers.php.
    public function searchQuery($request) {
        $query = [
            'model'         => 'Album',
            'searchModel'   => ['title', 'slug', 'description', 'image'],
            'searchRelated' => [
                'category' => ['name'],
                'photos'   => ['title', 'description', 'image', 'file', 'exif', 'iptc'],
                'posts'    => ['title', 'body', 'excerpt'],
                'tags'     => ['name'],
                'user'     => ['name', 'email']
            ],
            'sortModel'   => [
                'i'       => 'd,id',                                                      
                't'       => 'a,title',
                's'       => 'a,slug',
                'p'       => 'd,published',
                'c'       => 'd,created_at',
                'u'       => 'd,updated_at',
                'default' => 't'                       
            ],                                     
        ];
        return queryHelper($query, $request);
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
        $albums = $this->searchQuery($request)->paginate(10);
        if ($albums && $albums->count() > 0) {

        } else {
            Session::flash('failure', 'No Albums were found.');
        }
        return view('manage.albums.index', ['albums' => $albums, 'search' => $request->search, 'status_list' => $this->status()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $categories = Category::orderBy('name', 'asc')->pluck('name', 'id');
        $tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');
        $album = new Album;

        return view('manage.albums.create', ['album' => $album,
            'categories' => $categories, 'tags' => $tags, 'users' => $users, 'status_list' => $this->status(2)]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $this->validate($request, [
            'title'             => 'required|min:3|max:191',
            'slug'              => 'required|alpha_dash|min:3|max:191|unique:albums,slug',
            'category_id'       => 'required|integer|exists:categories,id',
            'image'             => 'required|image|mimes:jpeg,jpg,jpe,png,gif|max:10000|min:1',
            'description'       => 'required|min:3|max:2048',
            'author_id'         => 'sometimes|integer|exists:users,id',
            'status'            => 'required|integer|min:0|max:4',
            'tags'              => 'array',
            'tags.*'            => 'integer|exists:tags,id',
        ]);

        $album = new Album;
        $album->title            = $request->title;
        $album->slug             = $request->slug;
        $album->category_id      = $request->category_id;
        $album->description      = Purifier::clean($request->description);
        $album->status           = $request->status;
        
        $album->author_id = $request->author_id ? $request->author_id : auth()->user()->id;

        if ($album->status == '4') { $album->published_at = date('Y-m-d H:i:s'); }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = microtime() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images\\' . $filename);
            //$myrc = Image::make($image)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
            $myrc = Image::make($image)->widen(800, function ($constraint) { $constraint->upsize(); })->save($location);

            $album->image = $filename;
            $msgs[] = 'Image "' . $image->getClientOriginalName() . '" was successfully saved as ' . $filename;
        }

        $myrc = $album->save();

        if (isset($msgs)) { session()->flash('msgs', $msgs); }
        if ($myrc) {
            $myrc = $album->tags()->sync($request->tags, false);
            Session::flash('success', 'Album "' . $album->slug . '" was successfully saved.');
            return redirect()->route('albums.show', $album->id);
        } else {
            Session::flash('failure', 'Album "' . $id . '" was NOT saved.');
            return redirect()->route('albums.create', $id)->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $album = Album::findOrFail($id);

        $photos = $album->photos()->orderBy('title', 'asc')->paginate(5, ['*'], 'pageI');
        $posts  = $album->posts() ->orderBy('title', 'asc')->paginate(5, ['*'], 'pageP');
        $tags   = $album->tags()  ->orderBy('name',  'asc')->paginate(5, ['*'], 'pageT');

        if ($album) {
            return view('manage.albums.show', ['album' => $album, 'photos' => $photos, 'posts' => $posts, 'tags' => $tags, 'status_list' => $this->status()]);
        } else {
            Session::flash('failure', 'Album "' . $id . '" was NOT found.');
            return Redirect::back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $album = Album::findOrFail($id);

        $categories = Category::orderBy('name', 'asc')->pluck('name', 'id');
        $tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');

        if ($album) {
            return view('manage.albums.edit', ['album' => $album,
                    'categories' => $categories, 'tags' => $tags, 'users' => $users, 'status_list' => $this->status()]);
        } else {
            Session::flash('failure', 'Album "' . $id . '" was NOT found.');
            return Redirect::back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $album = Album::findOrFail($id);

        $this->validate($request, [
            'title'             => 'required|min:3|max:191',
            'slug'              => 'required|alpha_dash|min:3|max:191|unique:albums,slug,' . $id,
            'category_id'       => 'required|integer|exists:categories,id',
            'image'             => 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:10000|min:1',
            'description'       => 'required|min:3|max:2048',
            'author_id'         => 'sometimes|integer|exists:users,id',
            'status'            => 'required|integer|min:0|max:4',
            'tags'              => 'array',
            'tags.*'            => 'integer|exists:tags,id',
        ]);
    
        $album->title            = $request->title;
        $album->slug             = $request->slug;
        $album->category_id      = $request->category_id;
        $album->description      = Purifier::clean($request->description);
        $album->status           = $request->status;

        $album->author_id = $request->author_id ? $request->author_id : auth()->user()->id;

        if      ( $album->published_at && $album->status !== '4') { $album->published_at = null; }
        elseif  (!$album->published_at && $album->status  == '4') { $album->published_at = date('Y-m-d H:i:s'); }

        if ($request->hasFile('image')) {
            $oldFiles[]=$album->image;
            $image = $request->file('image');
            $filename = microtime() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images\\' . $filename);
            //Image::make($image)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
            $myrc = Image::make($image)->widen(800, function ($constraint) { $constraint->upsize(); })->save($location);
            $album->image = $filename;
            $msgs[] = 'Image "' . $image->getClientOriginalName() . '" was successfully saved as ' . $filename;
        } elseif ($request->delete_image) {
            //$oldFiles[] = $album->image;
            $msgs[] = 'Image "' . $album->image . '" may ONLY be deleted when changed for an alternative image.';
            //$album->image = null;
        } else {
            //$msgs[] = 'Image "' . $album->image . '" was successfully saved.';
        }

        $myrc = $album->save();

        if (isset($msgs)) { session()->flash('msgs', $msgs); }
        if ($myrc) {
            $myrc = $album->tags()->sync($request->tags, true);
            if (isset($oldFiles)) { Storage::delete($oldFiles); }
            Session::flash('success', 'Album "' . $album->slug . '" was successfully saved.');
            return redirect()->route('albums.show', $id);
        } else {
            Session::flash('failure', 'Album "' . $id . '" was NOT saved.');
            return redirect()->route('albums.edit', $id)->withInput();
        }
    }

    public function delete($id) {
        $album = Album::findOrFail($id);

        if ($album) {
            
        } else {
            Session::flash('failure', 'Album "' . $id . '" was NOT found.');
        }
        return view('manage.albums.delete', ['album'=>$album, 'status_list' => $this->status()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id) {
        $album = Album::findOrFail($id);

        // If delete_photos was checked, delete all UNSHARED photos in this album...
        // ...and issue warnings for all shared photos.  
        // If delete_photos was NOT checked and one or more photos would be left hanging...
        // without a parent album, then return an error. 
        foreach ($album->photos as $photo) {
            if ($request->delete_photos) {
                if ($photo->albums->count() == 1) { 
                    $photo->tags()->detach();
                    $photo->albums()->detach();
                    $myrc = $photo->delete();
                    if ($myrc) {
                        if ($photo->image) {
                            Storage::delete($photo->image);
                            Storage::delete($photo->file);
                            $msgx['info'][] = 'Image "' . $photo->image . '" was successfully deleted.';
                            $msgx['info'][] = 'Image "' . $photo->file  . '" was successfully deleted.';
                        }
                        $msgx['info'][] = 'Photo "' . $photo->title . '" was successfully deleted.';
                    } else {
                        $msgx['warning'][] = 'Photo "' . $id . '" was NOT found.';
                    }
                } else {
                    $msgx['warning'][] = 'Photo "' . $photo->title . '" is shared with another album and will NOT be deleted!';
                }
            } elseif ($photo->albums->count() == 1) {
                $msgx['failure'][] = 'Album "' . $album->title .
                    '" cannot be deleted as it contains UNSHARED photos. You must choose to delete the photos along with the abum!';
                if (isset($msgx)) { session()->flash('msgx', $msgx); }
                return Redirect::back()->withInput();
            }          
        }    

        // Now Delete the Album
        $album->tags()->detach();
        $myrc = $album->delete();
        if ($myrc) {
            if ($album->image) {
                Storage::delete($album->image);
                $msgx['info'   ][] = 'Image "' . $album->image . '" was successfully deleted.';
                $msgx['success'][] = 'Album "' . $album->slug .  '" was successfully deleted.';
            }
            if (isset($msgx)) { session()->flash('msgx', $msgx); }
            return redirect()->route('albums.index');
        } else {
            $msgx['failure'][] = 'Album "' . $id . '" was NOT found.';
            if (isset($msgx)) { session()->flash('msgx', $msgx); }
            return Redirect::back()->withInput();
        }
    }

    /**
     * API Check unique
     * We check if exists & modify it if it does. 
     */
    public function apiCheckUnique(Request $request) {
        $slug = $request->slug;
    
        for ($i=1; $i<100; $i++) {
            if (!Album::where('slug', '=', $slug)->exists()) {
                break;
            } else {
                if ($request->id) {
                    $album = Album::find($id);
                    if ($album->slug == $slug) {
                        break;
                    }   
                }   
                $slug = $request->slug . '-' . $i;
            } 
        }
        return json_encode($slug);
    }

}
