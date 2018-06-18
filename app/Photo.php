<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{

    public function albums() {
    	return $this->belongsToMany('App\Album');
    }

    public function tags() {
    	return $this->belongsToMany('App\Tag');
    }

}
