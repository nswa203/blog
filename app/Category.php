<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $table = 'categories';

    public function albums() {
    	return $this->hasMany('App\Album');
    }

    public function folders() {
        return $this->hasMany('App\Folder');
    }

    public function posts() {
    	return $this->hasMany('App\Post');
    }

}
