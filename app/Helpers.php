<?php

use App\Album;
use App\Category;
use App\Comment;
use App\Folder;
use App\Permission;
use App\Photo;
use App\Post;
use App\Profile;
use App\Role;
use App\Tag;
use App\User; 

// If you change Helpers.php you should do "dump-autoload". 
if (! function_exists('folderSize')) {
    function folderSize($dir) {
	    $total_size = 0;
	    $count = 0;
	    $dir_array = scandir($dir);
        foreach($dir_array as $key => $filename) {
        	if($filename != ".." && $filename != ".") {
           		if(is_dir($dir . "/" . $filename)) {
              		$new_foldersize = foldersize($dir . "/" . $filename);
              		$total_size = $total_size + $new_foldersize;
            	} else if(is_file($dir . "/" . $filename)) {
              		$total_size = $total_size + filesize($dir . "/" . $filename);
              		$count++;
            	}
       		}
 		}
		return $total_size;
	}
}

// If you change Helpers.php you should do "dump-autoload". 
if (! function_exists('msgx')) {
    function msgx($m=[]) {
    	if (!is_array($m)) { $m = ['Message' => [$m, true]]; }
        foreach ($m as $type => $rule) {
        	if (!array_key_exists(1, $rule)) { $rule[1] = true; }
        	if ($rule[1] == true) {
				$msgx = Session::get('msgx');
                $msgx[$type][] = $rule[0];
				session()->flash('msgx', $msgx);
        		break;
        	}
        } 
        return;
    }
}

// If you change Helpers.php you should do "dump-autoload". 
if (! function_exists('private_path')) {
    function private_path($folder='') {
        $folder = !$folder ? '' : '\\' . $folder;
        return base_path().'\\private' . $folder;
    }
}

// If you change Helpers.php you should do "dump-autoload".
// We don't hard code the model here so all the possible models that the
// controllers may request must be included up front
/*
	// Put the following near the top of your controller 
    public function searchQuery($search = '') {
        $query = [
            'model'         => 'Post',
            'searchModel'   => ['title', 'slug', 'image', 'body', 'excerpt'],
            'searchRelated' => [
				'user' 		=> ['name'],
				'category'  => ['name'],
				'tags' 		=> ['name'],
				'comments' 	=> ['email', 'name', 'comment' ]
            ],
            'filter'		=>['status', '>=', '4']
        ];
        return search_helper($search, $query);
    }

	// Put the following at the start of index function 
    $posts = $this->searchQuery($request->search)->orderBy('id', 'desc')->paginate(5);
*/ 
if (! function_exists('search_helper')) {
    function search_helper($search=false, $q) {
        $model = 'App\\' . $q['model'];
	    $query = $model::select('*');
        if (array_key_exists('filter', $q)) { 
           	$query = $query->where($q['filter'][0], $q['filter'][1], $q['filter'][2]);
        }

        if ($search) {
   	    	// Build an array of search terms where phrases bounded by "" are treated as a single term
           	$search_list = [];
            preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $search, $words);
            foreach ($words[0] as $word) {
                $search_list[] = trim($word, '"');
            }  

	    	// Eager load each related model
	        foreach ($q['searchRelated'] as $table => $columns) {
	        	$query=$query->with($table);
	        }

	    	// Search and filter each column in our model
	        foreach ($q['searchModel'] as $column) {
	            foreach ($search_list as $word) {
	                $query->orWhere($column, 'LIKE', '%' . $word . '%');
	                if (array_key_exists('filter', $q)) { 
	                	$query = $query->where($q['filter'][0], $q['filter'][1], $q['filter'][2]);
	                }
	            }    
	        }

	    	// Search and filter each column in related models
	        foreach ($q['searchRelated'] as $table => $columns) {
	            foreach ($columns as $column) {
	                foreach ($search_list as $word) {
	                    $query->orWhereHas($table, function($qq) use ($column, $search, $word, $q){
	                        $qq->where($column, 'LIKE', '%' . $word . '%');
	                        if (array_key_exists('filter', $q)) {
	                        	$qq = $qq->where($q['filter'][0], $q['filter'][1], $q['filter'][2]);
	                        }
	                    }); 
	                }
	            }
	        }
	    } 
	    return $query;
    }
}
