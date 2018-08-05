<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{

    /**
     * $search is routed to the appropriate controller via the Request.
     */
    public function index(Request $request) {
        $zone = session('zone');
        $search = $request->search ? 'search=' . $request->search : null;
     
        if      ($zone == 'Posts')      { $route = 'posts.index'; }
        elseif  ($zone == 'Comments')   { $route = 'comments.index'; }
        elseif  ($zone == 'Albums')     { $route = 'albums.index'; }
        elseif  ($zone == 'Photos')     { $route = 'photos.index'; }
        elseif  ($zone == 'Folders')    { $route = 'folders.index'; }
        elseif  ($zone == 'Files')      { $route = 'files.index'; }
        elseif  ($zone == 'Categories') { $route = 'categories.index'; }
        elseif  ($zone == 'Tags')       { $route = 'tags.index'; }
        elseif  ($zone == 'Users')      { $route = 'users.index'; }
        elseif  ($zone == 'Roles')      { $route = 'roles.index'; }
        elseif  ($zone == 'Permissions'){ $route = 'permissions.index'; }
        elseif  ($zone == 'Profiles')   { $route = 'profiles.index'; }

        else                            { $route = 'blog.index'; }

        return redirect()->route($route, $search);
    }

}
