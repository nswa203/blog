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

// Manage
Route::prefix('manage')->middleware('role:superadministrator|administrator|editor|author|contributor')->group(function () {
	// Dashboard
	Route::get('/dashboard', 'ManageController@dashboard')->name('manage.dashboard');
	Route::get('/',			 'ManageController@dashboard')->name('manage.dashboard');
	// Posts
	Route::resource('/posts', 'PostController');
	Route::get('/posts/{id}/delete', 'PostController@delete')->name('posts.delete');
	Route::post('/posts/search', 'PostController@search')->name('posts.search');		
	
});
Route::prefix('manage')->middleware('role:superadministrator|administrator')->group(function () {
	// Users
	Route::resource('/users', 'UserController');
	Route::get('users/{id}/delete',	'UserController@delete')->name('users.delete');
	// Roles
	Route::resource('/roles', 'RoleController');
	Route::get('roles/{id}/delete',	'RoleController@delete')->name('roles.delete');
	// Permissions
	Route::resource('/permissions', 'PermissionController');
	Route::get('permissions/{id}/delete', 'PermissionController@delete')->name('permissions.delete');
	// Categories
	Route::resource('categories', 'CategoryController')->except(['create']);
	Route::get('categories/{id}/delete', 'CategoryController@delete')->name('categories.delete');
	// Tags
	Route::resource('tags', 'TagController')->except(['create']);
	Route::get('tags/{id}/delete', 'TagController@delete')->name('tags.delete');
});

// Tests
Route::resource('tests', 'TestController');

// Pages
Route::get('contact',	'PagesController@getContact');
Route::post('contact',	'PagesController@postContact');
Route::get('about',		'PagesController@getAbout');
Route::get('blog',		'BlogController@getIndex')->name('blog.index');
Route::get('/',			'PagesController@getIndex');





// Comments
Route::post('comments/{post_id}',	'CommentsController@store'	)->name('comments.store');
Route::get('comments/{id}/edit',	'CommentsController@edit'	)->name('comments.edit');
Route::put('comments/{id}',			'CommentsController@update'	)->name('comments.update');
Route::delete('comments/{id}',		'CommentsController@destroy')->name('comments.destroy');
Route::get('comments/{id}/delete',	'CommentsController@delete'	)->name('comments.delete');
Route::get('comments',				'CommentsController@index'	)->name('comments.index');

// Auth
Auth::routes();
/* "home" is used as a default return URL within Laravel's built-in authentification 			*/
/* controllers. We don't have a "home", but rather than change multiple controllers we'll just 	*/
/* create a Route to handle it here by routing any "home" requests to to the same target as "/"	*/
// Route::get('home', 'PagesController@getIndex');
Route::get('/home', 'PagesController@getIndex')->name('home');

// Slugs
/* LAST LAST LAST LAST LAST LAST LAST LAST LAST LASTLAST LAST LAST LAST LASTLAST LAST LAST LAST */
/* Since we are not using any prefix, this route will intercept any routes placed after it. 	*/
/* So make it the LAST in your route list.														*/
/* We are also limiting what characters may be used in our slug to "a-z A-Z 0-9 - _ "       	*/
Route::get('/{slug}', 'BlogController@getSingle')->name('blog.single')->where('slug', '[\w\d\-\_]+');
