<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->group(function () {
	Route::get('/posts/unique',    'PostController@apiCheckUnique'  )->name('api.posts.unique'   );
	Route::get('/fo/unique',  'FolderController@apiCheckUnique')->name('api.folders.unique' );	    
	Route::get('/albums/unique',   'AlbumController@apiCheckUnique' )->name('api.albums.unique'  );
	Route::get('/files/elevation', 'FileController@apiGetElevation' )->name('api.files.elevation');
});
