<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('contact',	'PagesController@getContact');
Route::post('contact',	'PagesController@postContact');
Route::get('about',		'PagesController@getAbout');
Route::get('blog',		'BlogController@getIndex')->name('blog.index');
Route::get('/',			'PagesController@getIndex');

Route::resource('posts', 'PostController');
Route::resource('categories', 'CategoryController')->except(['create']);
Route::resource('tags', 'TagController')->except(['create']);;

Auth::routes();
/* "home" is used as a default return URL within Laravel's built-in authentification 			*/
/* controllers. We don't have a "home", but rather than change multiple controllers we'll just 	*/
/* create a Route to handle it here by routing any "home" requests to to the same target as "/"	*/
// Route::get('home', 'PagesController@getIndex');
Route::get('/home', 'PagesController@getIndex')->name('home');

/* LAST LAST LAST LAST LAST LAST LAST LAST LAST LASTLAST LAST LAST LAST LASTLAST LAST LAST LAST */
/* Since we are not using any prefix, this route will intercept any routes placed after it. 	*/
/* So make it the LAST in your route list.														*/
/* We are also limiting what characters may be used in our slug to "a-z A-Z 0-9 - _ "       	*/
Route::get('/{slug}', 'BlogController@getSingle')->name('blog.single')->where('slug', '[\w\d\-\_]+');
