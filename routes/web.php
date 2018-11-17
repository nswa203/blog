<?php
// Manage Administration
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
	Route::get('categories/{id}/delete',        'CategoryController@delete')->name('categories.delete');
	Route::get('categories/{category}/{zone?}', 'CategoryController@show'  )->name('categories.show');
	// Tags
	Route::resource('tags', 'TagController')->except(['create']);
	Route::get('tags/{id}/delete',   'TagController@delete')->name('tags.delete');
	Route::get('tags/{tag}/{zone?}', 'TagController@show'  )->name('tags.show');

	// Profiles
	Route::resource('/profiles', 'ProfileController')->except('create');
	Route::get('profiles/{id}/create', 'ProfileController@create')->name('profiles.create');
	Route::get('profiles/{id}/delete', 'ProfileController@delete')->name('profiles.delete');	


	// User's Posts
	Route::get('/pu/{name}', 'PageController@getIndexUserPost')->name('blog.getIndexUserPost');

});



// Manage
Route::prefix('manage')->middleware('role:superadministrator|administrator|editor|author|contributor')->group(function () {
	// Posts
	Route::resource('/posts', 'PostController');
	Route::get 	   ('/posts/{id}/delete', 'PostController@delete')->name('posts.delete');
	// Comments
	// Store can be found later in the table as it is not secured by middleware
	Route::resource('/comments', 'CommentController')->except(['store']);
	Route::get 	   ('/comments/{id}/delete', 'CommentController@delete')->name('comments.delete');
	// Folders
	Route::resource('/folders', 'FolderController');
	Route::get     ('/folders/{id}/delete', 'FolderController@delete')->name('folders.delete');
	// Files
	Route::resource('/files', 'FileController')->except('destroy', 'delete');
	Route::get ('/files/{id}/f', 	    'FileController@showFile'	   )->name('files.showFile');
	Route::get ('/files/{id}/createIn', 'FileController@createIn'	   )->name('files.createIn');
	Route::get ('/files/{id}/indexOf',  'FileController@indexOf' 	   )->name('files.indexOf');
	Route::post('/files/mixed',		    'FileController@mixed'   	   )->name('files.mixed');
	Route::post('/files/e',			    'FileController@updateMultiple')->name('files.updateMultiple');
	Route::post('/files/c',			    'FileController@copy'    	   )->name('files.copy');
	Route::post('/files/m',			    'FileController@move'    	   )->name('files.move');
	Route::post('/files/x',			    'FileController@destroy' 	   )->name('files.destroy');




	//Route::get     ('/comments/{id}/edit',   'CommentController@edit'   )->name('comments.edit');
	//Route::put     ('/comments/{id}',		 'CommentController@update' )->name('comments.update');
	//Route::delete  ('/comments/{id}',		 'CommentController@destroy')->name('comments.destroy');
	//Route::get     ('/comments/{id}/delete', 'CommentController@delete' )->name('comments.delete');
	//Route::get     ('/comments',			 'CommentController@index'  )->name('comments.index');






	// Dashboard
	Route::get('/dashboard', 'ManageController@dashboard')->name('manage.dashboard');
	Route::get('/',			 'ManageController@dashboard')->name('manage.dashboard');



	// Albums
	Route::resource('/albums', 'AlbumController');
	Route::get('/albums/{id}/delete', 'AlbumController@delete')->name('albums.delete');
	// Photos
	Route::resource('/photos', 'PhotoController');
	Route::get ('/photos/{id}/delete', 'PhotoController@delete'                )->name('photos.delete');
	Route::get ('/photos/{id}/image', 'PhotoController@showImage'              )->name('photos.showImage');
	Route::get ('/photos/{id}/createMultiple', 'PhotoController@createMultiple')->name('photos.createMultiple');
	Route::post('/photos/storeMultiple', 'PhotoController@storeMultiple'       )->name('photos.storeMultiple');
	// Search
	Route::post('search', 'SearchController@index')->name('search.index');

	// Private folders & files
	Route::get('private/{id}/{filename}',              'FolderController@getFolderFile')->name('private.getFolderFile');
	Route::get('private/{id}', 			               'FileController@getFile'        )->name('private.getFile');
	Route::get('private/find/{filename}/{foldername}', 'FileController@findFile'       )->name('private.findFile');
});

// Auth
// This adds login, logout, register and password reset routes
Auth::routes();

// Tests
Route::resource('tests', 'TestController');
Route::put('upload/{id}', 'TestController@upload')->name('tests.upload');

// Comments
Route::post('comments/{post_id}', 'CommentController@store')->name('comments.store');

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
