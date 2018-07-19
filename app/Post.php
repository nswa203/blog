<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
	
    public function albums() {
        return $this->belongsToMany('App\Album');
    }

    public function category() {
        return $this->belongsTo('App\Category');
    }

    public function comments() {
        return $this->hasMany('App\Comment');
    }   

    public function folders() {
        return $this->belongsToMany('App\Folder');
    }

    public function tags() {
        return $this->belongsToMany('App\Tag');
    }

    public function user() {
        return $this->belongsTo('App\User', 'author_id');
    }

}
