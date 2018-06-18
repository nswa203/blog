<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $table = 'categories';

    public function albums() {
    	return $this->hasMany('App\Album');
    }

    public function photos() {
    	return $this->hasMany('App\Photo');
    }

    public function posts() {
    	return $this->hasMany('App\Post');
    }

}
