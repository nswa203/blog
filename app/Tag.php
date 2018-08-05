<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    public function albums() {
    	return $this->belongsToMany('App\Album');
    }

    public function files() {
    	return $this->belongsToMany('App\File');
    }   

    public function photos() {
    	return $this->belongsToMany('App\Photo');
    }   

    public function posts() {
    	return $this->belongsToMany('App\Post');
    }

}
