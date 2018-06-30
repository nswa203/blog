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
use Exception;
use Illuminate\Support\Facades\Input;

class PhotoController extends Controller
{

    public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            session(['zone' => 'Photos']);
            return $next($request);
        });
    }

    // This Query Builder searches each table and each associated table for each word/phrase
    // It requires that SearchController pre loads Session('search_list')
    public function searchQuery($search = '') {
        $searchable1 = ['title', 'description', 'exif', 'iptc'];
        $searchable2 = ['tags' => ['name'], 'albums' => ['title', 'slug', 'description']];
        $query = Photo::select('*')->with('albums');

        if ($search !== '') {
            $search_list=session('search_list', []);
            foreach ($searchable1 as $column) {
                foreach ($search_list as $word) {
                    $query->orWhere($column, 'LIKE', '%' . $word . '%');
                }    
            }
            foreach ($searchable2 as $table => $columns) {
                foreach ($columns as $column) {
                    foreach ($search_list as $word) {
                        $query->orWhereHas($table, function($q) use ($column, $search, $word){
                            $q->where($column, 'LIKE', '%' . $word . '%');
                        }); 
                    }
                }
            }
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

    // UTF8 encode an array
    // $encoded_array = array_map('utf8_encode_array', $your_array);
    public function utf8_encode_deep(&$input) {
        if (is_string($input)) {
            $input = utf8_encode($input);
        } else if (is_array($input)) {
            foreach ($input as &$value) {
                $this->utf8_encode_deep($value);
            }
            unset($value);
        } else if (is_object($input)) {
            $vars = array_keys(get_object_vars($input));
                foreach ($vars as $var) {
                $this->utf8_encode_deep($input->$var);
            }
        }
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

        if ($photos) {

        } else {
            Session::flash('failure', 'No Photos were found.');
        }
        return view('manage.photos.index', ['photos' => $photos, 'search' => $request->search, 'status_list' => $this->status()]);
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
            $image    = $request->file('image');
            $rootFile = $image->getClientOriginalName();
            $newFile  = microtime() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images\\' . $newFile);

            // EXIF ------------------------------------------------------------------
            try { 
                $exif = Image::make($image)->exif();
                if (!$exif) { throw new Exception('EXIF Error 1.'); }
                $exif['FileName'] = $rootFile;
                // Check correct DateTime in EXIF data 
                if ($exif['DateTime']) {
                    $photo->taken_at = date('Y-m-d H:i:s', strtotime($exif['DateTime']));
                } 
                $work = json_encode($exif);
                if ($work) { $exif = $work; } else { $this->utf8_encode_deep($exif); $exif=json_encode($exif); }
                if (!$exif) { throw new Exception('EXIF Error 2.'); }
            }    
            catch (Exception $e) {
                $exif = '{"EXIF":"ERROR", "JSON":"' . json_last_error_msg() . '"}';
            } 
            $photo->exif = $exif;

            // IPTC ------------------------------------------------------------------
            try { 
                $iptc = Image::make($image)->iptc();
                if (!$iptc) { throw new Exception('IPTC Error 1.'); }
                $work = json_encode($iptc);
                if ($work) { $iptc = $work; } else { $this->utf8_encode_deep($iptc); ; $iptc=json_encode($iptc); }
                if (!$iptc) { throw new Exception('IPTC Error 2.'); }
            }    
            catch (Exception $e) {
                $iptc = '{}';
            } 
            $photo->iptc = $iptc;

            $myrc = Storage::putFileAs('original', $image, $newFile);
            if ($myrc) {
                $photo->file = 'original\\' . $newFile;
                $photo->size = $image->getClientsize();
            }
            $myrc = Image::make($image)->widen(800, function ($constraint) { $constraint->upsize(); })->save($location);
            if ($myrc) { $photo->image = $newFile;}
            $msgs[] = 'Image "' . $rootFile . '" was successfully saved as "' . $newFile .'"';
        }

        $myrc = $photo->save();

        if (isset($msgs)) { session()->flash('msgs', $msgs); }
        if ($myrc) {
            $myrc = $photo->tags()->sync($request->tags, false);
            $myrc = $photo->albums()->sync($request->album_ids, false);
            Session::flash('success', 'Photo "' . $photo->title . '" was successfully saved.');
            return redirect()->route('photos.show', $photo->id);
        } else {
            Session::flash('failure', 'Photo "' . $id . '" was NOT saved.');
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

        $meta = '<h4>EXIF:</h4>';
        $exif = json_decode($photo->exif, true)?:[];
        foreach ($exif as $key => $value) {
            if (gettype($value) !== 'array') {
                $meta = $meta . '<p>' . $key . ': ' . $value . '</p>';
            }
        }
        $meta = $meta . '<h4>IPTC:</h4>';
        $iptc = json_decode($photo->iptc, true)?:[];

        foreach ($iptc as $key => $value) {
            if (gettype($value) !== 'array') {
                $meta = $meta . '<p>' . $key . ': ' . $value . '</p>';
            }
        }        
        $exif['meta'] = $meta;

        $albums = $photo->albums()->orderBy('slug', 'asc')->paginate(5);

        if ($photo) {
            return view('manage.photos.show', ['photo' => $photo, 'albums' => $albums, 'status_list' => $this->status(), 'exif' => $exif]);
        } else Session::flash('failure', 'Photo "' . $id . '" was NOT found.');
            return Redirect::back();
    }

     /**
     * Display the specified resource.
     *
     * @param  \App\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function showImage($id) {
        $photo = Photo::findOrFail($id);

        if ($photo) {
            return view('manage.photos.showImage', ['photo' => $photo]);
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

        if ($photo) {
            return view('manage.photos.edit', ['photo' => $photo, 'tags' => $tags, 'albums' => $albums, 'status_list' => $this->status()]);
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
    public function update(Request $request, $id) {
        $photo = Photo::findOrFail($id);

        $this->validate($request, [
            'title'             => 'required|min:5|max:191',
            'image'             => 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:10000|min:1',
            'description'       => 'sometimes|max:2048',
            'status'            => 'required|integer|min:0|max:4',
            'album_ids'         => 'required|array|min:1',
            'album_ids.*'       => 'required|integer|exists:albums,id',
            'tags'              => 'array',
            'tags.*'            => 'integer|exists:tags,id',
        ]);
    
        $photo->title            = $request->title;
        $photo->description      = Purifier::clean($request->description);
        $photo->status           = $request->status;
        $photo->size             = '0';
        
        if      ( $photo->published_at && $photo->status !== '4') { $photo->published_at = null; }
        elseif  (!$photo->published_at && $photo->status  == '4') { $photo->published_at = date('Y-m-d H:i:s'); }

        if ($request->hasFile('image')) {
            $oldFiles[] = $photo->image;                                         // New image so delete old image
            $oldFiles[] = $photo->file;                                          // and its original file   
            $image      = $request->file('image');
            $rootFile   = $image->getClientOriginalName();
            $newFile    = microtime() . '.' . $image->getClientOriginalExtension();
            $location   = public_path('images\\' . $newFile);

            // EXIF ------------------------------------------------------------------
            try { 
                $exif = Image::make($image)->exif();
                if (!$exif) { throw new Exception('EXIF Error 1.'); }
                $exif['FileName'] = $rootFile;
                $work = json_encode($exif);
                if ($work) { $exif = $work; } else { $this->utf8_encode_deep($exif); $exif=json_encode($exif); }
                if (!$exif) { throw new Exception('EXIF Error 2.'); }
            }    
            catch (Exception $e) {
                $exif = '{"EXIF":"ERROR", "JSON":"' . json_last_error_msg() . '"}';
            } 
            $photo->exif = $exif;

            // IPTC ------------------------------------------------------------------
            try { 
                $iptc = Image::make($image)->iptc();
                if (!$iptc) { throw new Exception('IPTC Error 1.'); }
                $work = json_encode($iptc);
                if ($work) { $iptc = $work; } else { $this->utf8_encode_deep($iptc); ; $iptc=json_encode($iptc); }
                if (!$iptc) { throw new Exception('IPTC Error 2.'); }
            }    
            catch (Exception $e) {
                $iptc = '{}';
            } 
            $photo->iptc = $iptc;

            $myrc = Storage::putFileAs('original', $image, $newFile);
            if ($myrc) {
                $photo->file = 'original\\' . $newFile;
                $photo->size = $image->getClientsize();
            }
            $myrc = Image::make($image)->widen(800, function ($constraint) { $constraint->upsize(); })->save($location);
            if ($myrc) { $photo->image = $newFile;}
            $msgs[] = 'Image "' . $rootFile . '" was successfully saved as "' . $newFile .'"';
        } elseif ($request->delete_image) {
            $msgs[] = 'Image "' . $photo->image . '" may ONLY be deleted when changed for an alternative image.';
        } else {
            //$msgs[] = 'Image "' . $photo->image . '" was successfully saved.';
        }

        $myrc = $photo->save();

        if (isset($msgs)) { session()->flash('msgs', $msgs); }
        if ($myrc) {
            $myrc = $photo->tags()->sync($request->tags, true);
            $myrc = $photo->albums()->sync($request->album_ids, true);
            if (isset($oldFiles)) { Storage::delete($oldFiles); }
            Session::flash('success', 'Photo "' . $photo->title . '" was successfully saved.');
            return redirect()->route('photos.show', $id);
        } else {
            Session::flash('failure', 'Photo "' . $id . '" was NOT saved.');
            return redirect()->route('photos.edit', $id)->withInput();
        }
    }

    public function delete($id) {
        $photo = Photo::findOrFail($id);

        if ($photo) {
            
        } else {
            Session::flash('failure', 'Photo "' . $id . '" was NOT found.');
        }
        return view('manage.photos.delete', ['photo'=>$photo, 'status_list' => $this->status()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $photo = Photo::findOrFail($id);

        $photo->tags()->detach();
        $photo->albums()->detach();
        $myrc = $photo->delete();

        if ($myrc) {
            if ($photo->image) {
                Storage::delete($photo->image);
                Storage::delete($photo->file);
                $msgs[] = 'Image "' . $photo->image . '" was successfully deleted.';
                $msgs[] = 'Image "' . $photo->file  . '" was successfully deleted.';
            }
            Session::flash('success', 'Photo "' . $photo->title . '" was successfully deleted.');
            if (isset($msgs)) { session()->flash('msgs', $msgs); }
            return redirect()->route('albums.index');
        } else {
            Session::flash('failure', 'Photo "' . $id . '" was NOT found.');
            return Redirect::back();
        }
    }

    public function createMultiple($album_id) {
        $album = Album::findOrFail($album_id);
        $album_tags = $album->tags->pluck('id');
        $album->title = '%filename%';
        $album->description = '<h3>%title%</h3><p>%date%</p><p>%filename%</p>';

        $albums = Album::orderBy('slug', 'asc')->pluck('slug', 'id');
        $tags = Tag::orderBy('name', 'asc')->pluck('name', 'id');
        $photo = new Photo;
        $photos = collect([$photo]);

        return view('manage.photos.createMultiple', ['album' => $album, 'album_tags' => $album_tags, 'albums' => $albums, 'tags' => $tags, 'photo' => $photo, 'photos' => $photos, 'status_list' => $this->status(2)]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeMultiple(Request $request) {
        $this->validate($request, [
            'title'             => 'sometimes|max:191',
            'image'             => 'required|array|between:1,64',
            'image.*'           => 'filled|image|mimes:jpeg,jpg,jpe,png,gif|max:12000|min:1',
            'description'       => 'sometimes|max:2048',
            'status'            => 'required|integer|min:0|max:4',
            'album_ids'         => 'required|array|min:1',
            'album_ids.*'       => 'required|integer|exists:albums,id',
            'tags'              => 'array',
            'tags.*'            => 'integer|exists:tags,id',
        ]);

        $album_id = $request->album_ids[0];
        $countOK  = 0;
        $countBad = 0;
        $images = Input::file('image');
        foreach ($images as $image) {
            $photo = new Photo;
            $rootFile               = $image->getClientOriginalName();
            $photo->title           = $request->title;
            $photo->description     = Purifier::clean($request->description);
            $photo->status          = $request->status;
            $photo->size            = 0;
           
            if ($photo->status == '4') { $photo->published_at = date('Y-m-d H:i:s'); }
            $newFile  = microtime() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images\\' . $newFile);

            // EXIF ------------------------------------------------------------------
            try { 
                $exif = Image::make($image)->exif();
                if (!$exif) { throw new Exception('EXIF Error 1.'); }
                $exif['FileName'] = $rootFile;
                // Check correct DateTime in EXIF data 
                if ($exif['DateTime']) {
                    $photo->taken_at = date('Y-m-d H:i:s', strtotime($exif['DateTime']));
                } 
                $work = json_encode($exif);
                if ($work) { $exif = $work; } else { $this->utf8_encode_deep($exif); $exif=json_encode($exif); }
                if (!$exif) { throw new Exception('EXIF Error 2.'); }
            }    
            catch (Exception $e) {
                $exif = '{"EXIF":"ERROR", "JSON":"' . json_last_error_msg() . '"}';
            } 
            $photo->exif = $exif;

            // IPTC ------------------------------------------------------------------
            try { 
                $iptc = Image::make($image)->iptc();
                if (!$iptc) { throw new Exception('IPTC Error 1.'); }
                $work = json_encode($iptc);
                if ($work) { $iptc = $work; } else { $this->utf8_encode_deep($iptc); ; $iptc=json_encode($iptc); }
                if (!$iptc) { throw new Exception('IPTC Error 2.'); }
            }    
            catch (Exception $e) {
                $iptc = '{}';
            } 
            $photo->iptc = $iptc;

            // Replacements -----------------------------------------------------------
            $needles = ['%title%',    '%filename%', '%size',     '$date%',         '%album%'];
            $replace = [$photo->title, $rootFile,   $photo->size, $photo->taken_at, $album_id];
            $photo->title       = str_replace($needles, $replace, $photo->title);
            $photo->description = str_replace($needles, $replace, $photo->description);

            $myrc = Storage::putFileAs('original', $image, $newFile);
            if ($myrc) {
                $photo->file = 'original\\' . $newFile;
                $photo->size = $image->getClientsize();
            }

            $myrc = Image::make($image)->widen(800, function ($constraint) { $constraint->upsize(); })->save($location);
            if ($myrc) {
                $photo->image = $newFile;
                $myrc = $photo->save();
                $myrc = $photo->tags()->sync($request->tags, false);
                $myrc = $photo->albums()->sync($request->album_ids, false);            
            }
            if ($myrc) {
                $countOK++;
                $msgs[] = 'Image "' . $rootFile . '" was successfully saved as "' . $newFile .'"';
            } else { $countBad++; }

        }

        if (isset($msgs)) { session()->flash('msgs', $msgs); }
        if ($countOK  > 0) { Session::flash('success', $countOK  . ' Photos successfully saved.'); }
        if ($countBad > 0) { Session::flash('failure', $countBad . ' Photos were NOT saved.'); }
        if ($countOK  > 0) { 
            return redirect()->route('albums.show', $album_id);
        } else {
            Session::flash('failure', 'Photos were NOT saved.');
            return redirect()->route('manage.photos.createMultiple', $album_id)->withInput();
        }
    }

}
