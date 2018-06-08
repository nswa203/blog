<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $zone = session('zone');
        $search = $request->search ? $request->search : '';
        if ($zone == 'Posts')               {
            return redirect()->route('posts.index',         's=1')->with('search', $search);
        } elseif ($zone == 'Comments')      {
            return redirect()->route('comments.index',      's=1')->with('search', $search);
        } elseif ($zone == 'Categories')    {
            return redirect()->route('categories.index',    's=1')->with('search', $search);
        } elseif ($zone == 'Tags')          {
            return redirect()->route('tags.index',          's=1')->with('search', $search);
        } elseif ($zone == 'Users')         {
            return redirect()->route('users.index',         's=1')->with('search', $search);
        } elseif ($zone == 'Roles')         {
            return redirect()->route('roles.index',         's=1')->with('search', $search);
        } elseif ($zone == 'Permissions')   {
            return redirect()->route('permissions.index',   's=1')->with('search', $search);
        } else                              {
            return redirect()->route('blog.index',          's=1')->with('search', $search);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
