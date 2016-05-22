<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::group(['middleware'=>['web']],function(){


/*
* Just to verify the db connection
*/
Route::get('/example', 'DBController@');
//Delete this ^

	Route::get('/login','PageController@login');
	Route::post('/login','PageController@login');
	Route::post('/login_authorize','Auth\AuthController@validate_login');


	Route::get('/register','PageController@register');
	Route::post('/register','PageController@register');
	Route::post('/register_authorize','Auth\AuthController@validate_register');

	Route::get('/logout','PageController@logout');

	Route::get('/', function () {
		return redirect('/login');
	})->middleware('auth');

	Route::post('/mycontent','ContentController@show')->middleware('auth');


	Route::get('/mycontent/{page_number?}','ContentController@show')->middleware('auth');


	Route::get('/article/{type}/{api}','ContentController@article')->middleware('auth');

	Route::get('/settings','SettingsController@show')->middleware('auth');
Route::get('/recommended/{page_number?}','RecommendedController@show');



//Temporary




//Activate APIs
	Route::post('/authorize','PageController@authorizeAPI');

	Route::get('/authorize/github','GithubController@authorize');
	Route::get('/activate/github','GithubController@activate');

	Route::get('/authorize/pocket','PocketController@authorize');
	Route::get('/activate/pocket','PocketController@activate');

Route::get('/authorize/vimeo','VimeoController@authorize');
Route::get('/activate/vimeo','VimeoController@activate');

//Remove Routes
Route::post('/remove/{api}', 'PageController@removeAPI');

	Route::get('/authorize/slideshare','SlideshareController@authorize');
	Route::get('/activate/slideshare','SlideshareController@activate');

});
