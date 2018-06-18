<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{

    public function category() {
    	return $this->belongsTo('App\Category');
    }

    public function user() {
        return $this->belongsTo('App\User', 'author_id');
    }

    public function photo() {
        return $this->belongsToMany('App\Photo');
    }

    public function tags() {
    	return $this->belongsToMany('App\Tag');
    }

}
