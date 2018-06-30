<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{

    /**
     * $search is routed to the appropriate controller via the Request.
     * However, we also build an array of each word/phrase that is made
     * available as the 'search_list' Session variable.
     */
    public function index(Request $request) {
        $zone = session('zone');
        $search = $request->search ? 'search=' . $request->search : null;
       
        $search_list = [];
        if ($search) {
            preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $request->search, $words);
            foreach ($words[0] as $word) {
                $search_list[] = trim($word, '"');
            }  
        }
        session(['search_list' => $search_list]);
     
        if      ($zone == 'Posts')      { $route = 'posts.index'; }
        elseif  ($zone == 'Comments')   { $route = 'comments.index'; }
        elseif  ($zone == 'Categories') { $route = 'categories.index'; }
        elseif  ($zone == 'Tags')       { $route = 'tags.index'; }
        elseif  ($zone == 'Users')      { $route = 'users.index'; }
        elseif  ($zone == 'Roles')      { $route = 'roles.index'; }
        elseif  ($zone == 'Permissions'){ $route = 'permissions.index'; }
        elseif  ($zone == 'Profiles')   { $route = 'profiles.index'; }
        elseif  ($zone == 'Albums')     { $route = 'albums.index'; }
        elseif  ($zone == 'Photos')     { $route = 'photos.index'; }
        else                            { $route = 'blog.index'; }

        return redirect()->route($route, $search);
    }

}
