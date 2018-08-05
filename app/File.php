<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{

    public function folder() {
    	return $this->belongsTo('App\Folder');
    }


    public function tags() {
        return $this->belongsToMany('App\Tag');
    }

}
    