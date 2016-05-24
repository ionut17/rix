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


	Route::group(['middleware'=>['authgroup']],function(){
		Route::get('/', function () {
			return redirect('/login');
		});

		Route::post('/mycontent','ContentController@buildContent');
		Route::get('/mycontent','ContentController@buildContent');


		Route::get('/mycontent/{page_number?}','ContentController@show');


		Route::get('/article/{type}/{api}','ContentController@article');

		Route::get('/settings','SettingsController@show');
		Route::get('/recommended/{page_number?}','RecommendedController@show');

		Route::get('/refresh','SettingsController@refresh');
		Route::get('/search','ContentController@search');

		//Save filters
		Route::get('/filters','FilterController@get');
		Route::post('/filters','FilterController@save');


	//Activate APIs
		Route::post('/authorize','PageController@authorizeAPI');

		Route::get('/authorize/github','GithubController@authorize');
		Route::get('/activate/github','GithubController@activate');

		Route::get('/authorize/pocket','PocketController@authorize');
		Route::get('/activate/pocket','PocketController@activate');

		Route::get('/authorize/vimeo','VimeoController@authorize');
		Route::get('/activate/vimeo','VimeoController@activate');

		Route::get('/authorize/slideshare','SlideshareController@authorize');

	//Remove Routes
		Route::post('/remove/{api}', 'PageController@removeAPI');

	});
});
