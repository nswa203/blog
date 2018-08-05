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
	// Folders
	Route::resource('/folders', 'FolderController');
	Route::get('/folders/{id}/delete', 'FolderController@delete')->name('folders.delete');
	// Files
	Route::resource('/files', 'FileController');
	Route::get('/files/{id}/delete', 'FileController@delete')->name('files.delete');
	Route::get('/files/{id}/image', 'FileController@showFile')->name('files.showFile');
	Route::get('/files/{id}/createIn', 'FileController@createIn')->name('files.createIn');
	Route::get('/files/{id}/indexOf', 'FileController@indexOf')->name('files.indexOf');
	// Albums
	Route::resource('/albums', 'AlbumController');
	Route::get('/albums/{id}/delete', 'AlbumController@delete')->name('albums.delete');
	// Photos
	Route::resource('/photos', 'PhotoController');
	Route::get('/photos/{id}/delete', 'PhotoController@delete')->name('photos.delete');
	Route::get('/photos/{id}/image', 'PhotoController@showImage')->name('photos.showImage');
	Route::get('/photos/{id}/createMultiple', 'PhotoController@createMultiple')->name('photos.createMultiple');
	Route::post('/photos/storeMultiple', 'PhotoController@storeMultiple')->name('photos.storeMultiple');
	// Search
	Route::post('search', 'SearchController@index')->name('search.index');
	// Comments
	Route::get('/comments/{id}/edit',	'CommentsController@edit'	)->name('comments.edit');
	Route::put('/comments/{id}',		'CommentsController@update'	)->name('comments.update');
	Route::delete('/comments/{id}',		'CommentsController@destroy')->name('comments.destroy');
	Route::get('/comments/{id}/delete',	'CommentsController@delete'	)->name('comments.delete');
	Route::get('/comments',				'CommentsController@index'	)->name('comments.index');
	// Private folders & files
	Route::get('private/{id}/{filename}', 'FolderController@getFolderFile')->name('private.getFolderFile');
	Route::get('private/{id}', 			  'FileController@getFile'        )->name('private.getFile');
});
Route::prefix('manage')->middleware('role:superadministrator|administrator')->group(function () {
	// Users
	Route::resource('/users', 'UserController');
	Route::get('users/{id}/delete',	'UserController@delete')->name('users.delete');
	// Profiles
	Route::resource('/profiles', 'ProfileController')->except('create');
	Route::get('profiles/{id}/create',	'ProfileController@create'	)->name('profiles.create');
	Route::get('profiles/{id}/delete',	'ProfileController@delete')->name('profiles.delete');	
	// Roles
	Route::resource('/roles', 'RoleController');
	Route::get('roles/{id}/delete',	'RoleController@delete')->name('roles.delete');
	// Permissions
	Route::resource('/permissions', 'PermissionController');
	Route::get('permissions/{id}/delete', 'PermissionController@delete')->name('permissions.delete');
	// Categories
	Route::resource('categories', 'CategoryController')->except(['create']);
	Route::get('categories/{id}/delete', 'CategoryController@delete')->name('categories.delete');
	Route::get('categories/{category}/{zone?}', 'CategoryController@show')->name('categories.show');
	// Tags
	Route::resource('tags', 'TagController')->except(['create']);
	Route::get('tags/{id}/delete', 'TagController@delete')->name('tags.delete');
	Route::get('tags/{tag}/{zone?}', 'TagController@show')->name('tags.show');
});

// Tests
Route::resource('tests', 'TestController');

// Comments
Route::post('comments/{post_id}',	'CommentsController@store'	)->name('comments.store');
//Route::get('comments/{id}/edit',	'CommentsController@edit'	)->name('comments.edit');
//Route::put('comments/{id}',			'CommentsController@update'	)->name('comments.update');
//Route::delete('comments/{id}',		'CommentsController@destroy')->name('comments.destroy');
//Route::get('comments/{id}/delete',	'CommentsController@delete'	)->name('comments.delete');
//Route::get('comments',				'CommentsController@index'	)->name('comments.index');

// Auth
Auth::routes();
/* "home" is used as a default return URL within Laravel's built-in authentification 			*/
/* controllers. We don't have a "home", but rather than change multiple controllers we'll just 	*/
/* create a Route to handle it here by routing any "home" requests to to the same target as "/"	*/
// Route::get('home', 'PagesController@getIndex');
Route::get('/home', 'PagesController@getIndex')->name('home');

// Pages (Public routes)
Route::get('contact',	'PagesController@getContact');
Route::post('contact',	'PagesController@postContact');
Route::get('about',		'PagesController@getAbout');
Route::get('blog',		'PagesController@getIndexPost')->name('blog.index');
Route::get('/',			'PagesController@getHomePost');

Route::get('/t/{tag}',	'PagesController@getIndexTagPost')->name('blog.indexTagPost')->where('tag',  '[\w\d\-\_]+');
Route::get('/a/{slug}', 'PagesController@getSingleAlbum') ->name('blog.singleAlbum') ->where('slug', '[\w\d\-\_]+');
Route::get('/i/{id}', 	'PagesController@getSinglePhoto') ->name('blog.singlePhoto');
Route::get('/f/{slug}', 'PagesController@getSingleFolder')->name('blog.singleFolder')->where('slug', '[\w\d\-\_]+');
Route::get('/fi/{id}',  'PagesController@getSingleFile')  ->name('blog.singleFile');
/* LAST LAST LAST LAST LAST LAST LAST LAST LAST LASTLAST LAST LAST LAST LASTLAST LAST LAST LAST */
/* Since we are not using any prefix, this route will intercept any routes placed after it. 	*/
/* So make it the LAST in your route list.														*/
/* We are also limiting what characters may be used in our slug to "a-z A-Z 0-9 - _ "       	*/
Route::get('/{slug}',	'PagesController@getSinglePost')  ->name('blog.singlePost')  ->where('slug', '[\w\d\-\_]+');
