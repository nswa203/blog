<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{

    public function category() {
    	return $this->belongsTo('App\Category');
    }

    public function files() {
        return $this->hasMany('App\File');
    }

    public function posts() {
        return $this->belongsToMany('App\Post');
    }

    public function profiles() {
        return $this->belongsToMany('App\Profile');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

}
