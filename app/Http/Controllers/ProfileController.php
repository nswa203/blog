<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Profile;
use App\User;
use Purifier;
use Session;
use Image;
use Storage;

function searchQuery($search = '') {
    $searchable1 = ['username', 'about_me', 'phone', 'address'];
    $searchable2 = ['user' => ['name', 'email']];
    $query = Profile::select('*')->with('user');

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

class ProfileController extends Controller
{
   public function __construct(Request $request)
    {
        $this->middleware(function ($request, $next) {
            session(['zone' => 'Profiles']);
            return $next($request);
        });
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->search) {
            $profiles = searchQuery($request->search)->orderBy('username', 'asc')->paginate(10);
        } else {
            $profiles = Profile::orderBy('username', 'asc')->with('user')->paginate(10);
        }   

        if ($profiles) {

        } else {
            Session::flash('failure', 'No Profiles were found.');
        }
        return view('manage.profiles.index', ['profiles' => $profiles, 'search' => $request->search]);    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::doesntHave('profile')->orderBy('name', 'asc')->pluck('name', 'id');
        $profile = new Profile;

        return view('manage.profiles.create', ['profile' => $profile, 'users' => $users]);    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'username'          => 'required|min:3|max:191|unique:profiles,username',
            'image'             => 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
            'banner'            => 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
            'about_me'          => 'sometimes|max:1024',
            'phone'             => 'sometimes|max:191',
            'address'           => 'sometimes|max:191',
            'user_id'           => 'sometimes|integer|exists:users,id|unique:profiles,user_id',
        ]);

        $profile = new Profile;
        $profile->username         = $request->username;
        $profile->phone            = $request->phone;
        $profile->address          = Purifier::clean($request->address);
        $profile->about_me         = Purifier::clean($request->about_me);
        $profile->user_id          = $request->user_id ? $request->user_id : auth()->user()->id;  

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = microtime() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images\\' . $filename);
            Image::make($image)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
            $profile->image = $filename;
            $msgs[] = 'Image "' . $image->getClientOriginalName() . '" was successfully saved as ' . $filename;
        }
        if ($request->hasFile('banner')) {
            $image = $request->file('banner');
            $filename = microtime() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images\\' . $filename);
            Image::make($image)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
            $profile->banner = $filename;
            $msgs[] = 'Image "' . $image->getClientOriginalName() . '" was successfully saved as ' . $filename;
        }

        $myrc = $profile->save();

        if (isset($msgs)) { session()->flash('msgs', $msgs); }
        if ($myrc) {
            Session::flash('success', 'Profile "' . $profile->username . '" was successfully saved.');
            return redirect()->route('profiles.show', $profile->id);
        } else {
            Session::flash('failure', 'Profile "' . $profile->username . '" was NOT saved.');
            return redirect()->route('profiles.create', $id)->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $profile = Profile::with('user')->findOrFail($id);

        if ($profile) {
            return view('manage.profiles.show', ['profile' => $profile]);
        } else {
            Session::flash('failure', 'Profile "' . $id . '" was NOT found.');
            return Redirect::back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $profile = Profile::with('user')->findOrFail($id);
        $users = [$profile->user->id => $profile->user->name];

        if ($profile) {
            return view('manage.profiles.edit', ['profile' => $profile, 'users' => $users]);
        } else {
            Session::flash('failure', 'Profile "' . $id . '" was NOT found.');
            return Redirect::back();
        }
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
        $profile = Profile::findOrFail($id);

        $this->validate($request, [
            'username'          => 'required|min:3|max:191|unique:profiles,username,' . $id,
            'image'             => 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
            'banner'            => 'sometimes|image|mimes:jpeg,jpg,jpe,png,gif|max:8000|min:1',
            'about_me'          => 'sometimes|max:1024',
            'phone'             => 'sometimes|max:191',
            'address'           => 'sometimes|max:191',
        ]);

        $profile->username         = $request->username;
        $profile->phone            = $request->phone;
        $profile->address          = Purifier::clean($request->address);
        $profile->about_me         = Purifier::clean($request->about_me);

        if ($request->hasFile('image')) {
            $oldFiles[]=$profile->image;
            $image = $request->file('image');
            $filename = microtime() . '.' . $image->getClientOriginalExtension();
            $location = public_path('images\\' . $filename);
            Image::make($image)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
            $profile->image = $filename;
            $msgs[] = 'Image "' . $image->getClientOriginalName() . '" was successfully saved as ' . $filename;
        } elseif ($request->delete_image) {
            $oldFiles[] = $profile->image;
            $msgs[] = 'Image "' . $profile->image . '" deleted.';
            $profile->image = null;
        } else {
            //$msgs[] = 'Image "' . $profile->image . '" was successfully saved.';
        }

        if ($request->hasFile('banner')) {
            $oldFiles[] = $profile->banner;
            $banner = $request->file('banner');
            $filename = microtime() . '.' . $banner->getClientOriginalExtension();
            $location = public_path('images\\' . $filename);
            Image::make($banner)->resize(800, null, function ($constraint) { $constraint->aspectRatio(); })->save($location);
            $profile->banner = $filename;
            $msgs[] = 'Image "' . $banner->getClientOriginalName() . '" was successfully saved as ' . $filename;
        } elseif ($request->delete_banner) {
            $oldFiles[] = $profile->banner;
            $msgs[] = 'Image "' . $profile->banner . '" deleted.';
            $profile->banner = null;
        } else {
            //$msgs[] = 'Image "' . $profile->banner . '" was successfully saved.';
        }

        $myrc = $profile->save();

        if (isset($msgs)) { session()->flash('msgs', $msgs); }
        if ($myrc) {
            if (isset($oldFiles)) { Storage::delete($oldFiles); }
            Session::flash('success', 'Profile "' . $profile->username . '" was successfully saved.');
            return redirect()->route('profiles.show', $id);
        } else {
            Session::flash('failure', 'Profile "' . $id . '" was NOT saved.');
            return redirect()->route('profiles.edit', $id)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id) {
        $profile = Profile::findOrFail($id);

        if ($profile) {
            
        } else {
            Session::flash('failure', 'Profile ' . $id . ' was NOT found.');
        }
        return view('manage.profiles.delete', ['profile' => $profile]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $profile = Profile::findOrFail($id);
        $myrc = $profile->delete();

        if ($myrc) {
            if ($profile->image) {
                Storage::delete($profile->image);
                $msgs[] = 'Image "' . $profile->image . '" was successfully deleted.';
            }
            if ($profile->banner) {
                Storage::delete($profile->banner);
                $msgs[] = 'Image "' . $profile->banner . '" was successfully deleted.';
            }
            Session::flash('success', 'Profile ' . $profile->username . ' was successfully deleted.');
            if (isset($msgs)) { session()->flash('msgs', $msgs); }
            return redirect()->route('profiles.index');
        } else {
            Session::flash('failure', 'Profile ' . $id . ' was NOT found.');
            return Redirect::back();
        }
    }
}
