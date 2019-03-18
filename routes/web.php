<?php
// Auth
// This adds login, logout, register and password reset routes
Auth::routes();

Route::middleware('auth')->group(function() { 							// Must be logged in
	// Password
	Route::get ('/changePassword', 'HomeController@changePassword')->name('changePassword');
	Route::post('/changePassword', 'HomeController@updatePassword')->name('updatePassword');
	// Search
	Route::post('/search', 'SearchController@index')->name('search.index');
});

// Manage 
Route::prefix('manage')->middleware('auth')->group(function() { 		// Must be logged in
	// Dashboard
	Route::get('/dashboard', 'ManageController@dashboard')->name('manage.dashboard');
	Route::get('/',			 'ManageController@dashboard')->name('manage.dashboard');
	// Users
	Route::resource('/users', 'UserController');
	Route::get('users/{user}/delete', 'UserController@delete')->name('users.delete');
	// Roles
	Route::resource('/roles', 'RoleController');
	Route::get('roles/{role}/delete', 'RoleController@delete')->name('roles.delete');
	// Permissions
	Route::resource('/permissions', 'PermissionController');
	Route::get('permissions/{permission}/delete', 'PermissionController@delete')->name('permissions.delete');
	// Categories
	Route::resource('categories', 'CategoryController')->except(['create']);
	Route::get('categories/{category}/delete',  'CategoryController@delete')->name('categories.delete');
	Route::get('categories/{category}/{zone?}', 'CategoryController@show'  )->name('categories.show');
	// Tags
	Route::resource('tags', 'TagController')->except(['create']);
	Route::get('tags/{tag}/delete',  'TagController@delete')->name('tags.delete');
	Route::get('tags/{tag}/{zone?}', 'TagController@show'  )->name('tags.show');
	// Profiles
	Route::resource('/profiles', 'ProfileController')->except('create');
	Route::get('profiles/{user}/create',    'ProfileController@create')->name('profiles.create');
	Route::get('profiles/{profile}/delete', 'ProfileController@delete')->name('profiles.delete');
	// Comments
	// Store can be found later in the table as it is not secured by middleware
	Route::resource('/comments', 'CommentController')->except(['store']);
	Route::get 	   ('/comments/{comment}/delete', 'CommentController@delete')->name('comments.delete');
	// Posts
	Route::resource('/posts', 'PostController');
	Route::get 	   ('/posts/{post}/delete', 'PostController@delete')->name('posts.delete');
	// Folders
	Route::resource('/folders', 'FolderController');
	Route::get     ('/folders/{folder}/delete',     'FolderController@delete'       )->name('folders.delete');
	Route::get     ('/folders/{folder}/{filename}', 'FolderController@getFolderFile')->name('folders.getFolderFile');
	// Files
	Route::resource('/files', 'FileController')->except('destroy', 'delete');
	Route::get ('/files/{file}/f', 	      'FileController@showFile'	      )->name('files.showFile');
	Route::get ('/files/{file}/createIn', 'FileController@createIn'	      )->name('files.createIn');
	Route::get ('/files/{file}/indexOf',  'FileController@indexOf' 	      )->name('files.indexOf');
	Route::post('/files/many',		      'FileController@many'   	  )->name('files.many');
	Route::post('/files/e',			      'FileController@manyEdit'   )->name('files.manyEdit');
	Route::post('/files/c',			      'FileController@manyCopy'   )->name('files.manyCopy');
	Route::post('/files/m',			      'FileController@manyMove'   )->name('files.manyMove');
	Route::post('/files/d',			      'FileController@manyDestroy')->name('files.manyDestroy');	
});

// Comments
Route::post('comments/{post}', 'CommentController@store')->name('comments.store');




// Manage
Route::prefix('manage')->middleware('role:superadministrator|administrator|editor|author|subscriber|guest')->group(function () {





	// User's Posts
	Route::get('/pu/{name}', 'PageController@getIndexUserPost')->name('blog.getIndexUserPost');

	//Route::get     ('/comments/{id}/edit',   'CommentController@edit'   )->name('comments.edit');
	//Route::put     ('/comments/{id}',		 'CommentController@update' )->name('comments.update');
	//Route::delete  ('/comments/{id}',		 'CommentController@destroy')->name('comments.destroy');
	//Route::get     ('/comments/{id}/delete', 'CommentController@delete' )->name('comments.delete');
	//Route::get     ('/comments',			 'CommentController@index'  )->name('comments.index');









	// Albums
	Route::resource('/albums', 'AlbumController');
	Route::get('/albums/{album}/delete', 'AlbumController@delete')->name('albums.delete');
	// Photos
	Route::resource('/photos', 'PhotoController');
	Route::get ('/photos/{photo}/delete', 'PhotoController@delete'                )->name('photos.delete');
	Route::get ('/photos/{photo}/image', 'PhotoController@showImage'              )->name('photos.showImage');
	Route::get ('/photos/{photo}/createMultiple', 'PhotoController@createMultiple')->name('photos.createMultiple');
	Route::post('/photos/storeMultiple', 'PhotoController@storeMultiple'       )->name('photos.storeMultiple');


	// Private folders & files
	Route::get('private/{id}', 			               'FileController@getFile'        )->name('private.getFile');
	Route::get('private/find/{filename}/{foldername}', 'FileController@findFile'       )->name('private.findFile');
});








// Tests
Route::resource('/tests', 'TestController');
Route::post('/tests/upload', 'TestController@upload')->name('tests.upload');

// Comments
Route::post('comments/{post}', 'CommentController@store')->name('comments.store');

/* "home" is used as a default return URL within Laravel's built-in authentification 			*/
/* controllers. We don't have a "home", but rather than change multiple controllers we'll just 	*/
/* create a Route to handle it here by routing any "home" requests to to the same target as "/"	*/
// Route::get('home', 'PageController@getIndex');
Route::get('/home', 'PageController@showHome')->name('home');

// Pages (Public routes)
Route::get ('contact', 'PageController@showContact')->name('blog.contact');
Route::post('contact', 'PageController@postContact')->name('blog.email'  );
Route::get ('about',   'PageController@showAbout'  )->name('blog.about'  );
Route::get ('blog',	   'PageController@indexPost'  )->name('blog.index'  );

Route::get ('fo/{id}', 'PageController@showFolder' )->name('blog.folder' );
Route::get ('po/{id}', 'PageController@showPost'   )->name('blog.post'   );
Route::get ('al/{id}', 'PageController@showAlbum'  )->name('blog.album'  );
Route::get ('fi/{id}', 'PageController@showFile'   )->name('blog.file'   );
Route::get ('ph/{id}', 'PageController@showPhoto'  )->name('blog.photo'  );

Route::get ('/{slug}', 'PageController@showPost'   )->name('blog.post'   )->where('slug', '[\w\d\-\_]+');
/* LAST LAST LAST LAST LAST LAST LAST LAST LAST LASTLAST LAST LAST LAST LASTLAST LAST LAST LAST */
/* Since we are not using any prefix, this route will intercept any routes placed after it. 	*/
/* So make it the LAST in your route list.														*/
/* We are also limiting what characters may be used in our slug to "a-z A-Z 0-9 - _ "       	*/
Route::get('/',	       'PageController@showHome'    )->name('blog.home');
