<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Tag;
use App\Category;
use App\Post;
use App\User;
use Session;
use Storage;
use URL;

function status($default = -1) {
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

class TestController extends Controller
{

    public function __construct() {
        $this->middleware('auth');
    }

    public function gpx() {
        $pos1 = ['496497', '165478'];
        $pos2 = ['496517', '165560'];
        $pos3 = ['497024', '162952'];
        $pos4 = ['490000', '160000'];

        $OSRefs = OSRefs([$pos1, $pos2, $pos3, $pos4], 50, true); // {{Easting, Northing}...{Easting, Northing}} [Distances] [Elevations]
        
        $data =  [];
        $i = 0;
        $d = 0;
        foreach ($OSRefs as $OSRef) {
            $x = isset($OSRef->d) ? $OSRef->d : 0;
            $d = $d + $x;
            $y = $OSRef->getH();
            $data[] = ['x'=>round($d, 1), 'y'=>$y];
            $minY = $i==0 ? $y : ($y<$minY ? $y : $minY);
            $maxY = $i==0 ? $y : ($y>$maxY ? $y : $maxY);
            ++$i;
        }
        $minY = $minY<=10 ? 0 : $minY - 10;
        $minY = ceil($minY / 10) * 10;
        $maxY = ceil(($maxY + 10) / 10) * 10;
        $range = [0, round($d, 0), $minY, $maxY];

        return ['data' => $data, 'range' => $range];        
    }    

    public function upload(Request $request, $id) {
        $type = $request->ajax() ? 'AJAX' : 'HTTP';
        $files = Input::file('files');
        if ($files) {
            $countBad = count($files);
            $countOK  = 0;
            foreach ($files as $file) {
                ++$countOK;
                --$countBad;
                //break;
                sleep(1);
            }
        } else {
            $countBad = 0;
        }    

        if ($countBad == 0) {
            Session::flash('success', 'All Files were successfully uploaded for "'.$id.'". Type='.$type);
            return json_encode(['countBad' => $countBad, 'url' => route('tests.index')]);    
        } else {
            Session::flash('failure', 'Oops! Something went wrong.');
            return json_encode(['countBad' => $countBad]);    
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('tests.index',['tests'=>null]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $categories=Category::orderBy('name','asc')->pluck('name','id');
        $tags=Tag::orderBy('name','asc')->pluck('name','id');
        return view('tests.create',['categories'=>$categories,'tags'=>$tags]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $results = $this->gpx();
        return view('tests.show',['test' => $id, 'gdata' => $results['data'], 'grange' => $results['range']]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $categories=Category::orderBy('name','asc')->pluck('name','id');
        $tags=Tag::orderBy('name','asc')->pluck('name','id');
        $users=Tag::orderBy('name','asc')->pluck('name','id');
        return view('tests.create', ['post' => $post, 'categories' => $categories, 'tags' => $tags, 'users' => $users, 'status_list' => status(2)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $post = Post::findOrFail($id);

        $this->validate($request, [
            'title'             => 'required|min:8|max:191',
            'slug'              => 'required|alpha_dash|min:5|max:191|unique:posts,slug,' . $id,
            'category_id'       => 'required|integer|exists:categories,id',
            'image'             => 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
            'banner'            => 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
            'body'              => 'required|min:1',
            'excerpt'           => 'sometimes',
            'author_id'         => 'sometimes|integer|exists:users,id',
            'status'            => 'required|integer|min:0|max:4',
            'tags'              => 'array',
            'tags.*'            => 'integer|exists:tags,id',
        ]);
    
        $post->title            = $request->title;
        $post->slug             = $request->slug;
        $post->category_id      = $request->category_id;
        $post->body             = Purifier::clean($request->body);
        $post->status           = $request->status;

        $post->excerpt = $request->excerpt ? $request->excerpt : $request->body; 
        $post->excerpt = Purifier::clean(strip_tags($post->excerpt));
        $post->excerpt = strlen($post->excerpt)<=253 ? $post->excerpt : substr($post->excerpt, 0, 256) . '...';  
        
        $post->author_id = $request->author_id ? $request->author_id : auth()->user()->id;

        if      ( $post->published_at && $post->status !== '4') { $post->published_at = null; }
        elseif  (!$post->published_at && $post->status  == '4') { $post->published_at = date('Y-m-d H:i:s'); }

        if ($request->hasFile('image')) {
            $oldFiles[]=$post->image;
            $image = $request->file('image');
            $filename = microtime() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images\\' . $filename);
            Image::make($image)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
            $post->image = $filename;
            $msgs[] = 'Image "' . $image->getClientOriginalName() . '" was successfully saved as ' . $filename;
        } elseif ($request->delete_image) {
            $oldFiles[] = $post->image;
            $msgs[] = 'Image "' . $post->image . '" deleted.';
            $post->image = null;
        } else {
            //$msgs[] = 'Image "' . $post->image . '" was successfully saved.';
        }

        if ($request->hasFile('banner')) {
            $oldFiles[] = $post->banner;
            $banner = $request->file('banner');
            $filename = microtime() . '.' . $banner->getClientOriginalExtension();
            $location = public_path('images\\' . $filename);
            Image::make($banner)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
            $post->banner = $filename;
            $msgs[] = 'Image "' . $banner->getClientOriginalName() . '" was successfully saved as ' . $filename;
        } elseif ($request->delete_banner) {
            $oldFiles[] = $post->banner;
            $msgs[] = 'Image "' . $post->banner . '" deleted.';
            $post->banner = null;
        } else {
            //$msgs[] = 'Image "' . $post->banner . '" was successfully saved.';
        }

        $myrc = $post->save();

        if (isset($msgs)) { session()->flash('msgs', $msgs); }
        if ($myrc) {
            $myrc = $post->tags()->sync($request->tags, true);
            if (isset($oldFiles)) { Storage::delete($oldFiles); }
            Session::flash('success', 'Post "' . $post->slug . '" was successfully saved.');
            return redirect()->route('posts.show', $id);
        } else {
            Session::flash('failure', 'Post "' . $id . '" was NOT saved.');
            return redirect()->route('posts.edit', $id)->withInput();
        }
    }    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
}
