<?php namespace App\myLibs;

// When using in, say a Controller ...
// use App\myLibs\TestClass as Test;
// $t = new Test;
// $t->announce();

class TestClass {

    public function announce() {
        echo 'Hello World!';
    }

}