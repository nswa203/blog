<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{

    public function category() {
    	return $this->belongsTo('App\Category');
    }

    public function photos() {
        return $this->belongsToMany('App\Photo');
    }

    public function posts() {
        return $this->belongsToMany('App\Post');
    }
    
    public function tags() {
    	return $this->belongsToMany('App\Tag');
    }

    public function user() {
        return $this->belongsTo('App\User', 'author_id');
    }

}
