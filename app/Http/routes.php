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

//register ajax route
	Route::get('/validator','Auth\AuthController@ajax_validator');
	Route::post('/register_authorize','Auth\AuthController@validate_register');

	Route::get('/logout','PageController@logout');

//API Rest
	Route::get('/api/{token}/{service}/get', 'APIController@call');
	Route::get('/api/{token}/{service}/deleteaccount','APIController@delete_account');
	Route::post('/api/{token}/{service}/connect','APIController@connect');
	Route::get('/generatetoken','APIController@get_token');

	Route::group(['middleware'=>['authgroup']],function(){
		Route::get('/', function () {
			return redirect('/login');
		});

		Route::post('/mycontent','ContentController@buildContent');
		Route::get('/mycontent','ContentController@buildContent');


		Route::get('/mycontent/{page_number?}','ContentController@show');


		Route::get('/article/{type}/{api}','ContentController@article');

		Route::get('/settings','SettingsController@show');
		Route::post('/settings/modify','SettingsController@modify');

		Route::get('/recommended/{page_number?}','RecommendedController@buildRecommendedContent');

		Route::get('/refresh','SettingsController@refresh');
		Route::get('/search','ContentController@search');

		//Save filters
		Route::get('/filters','FilterController@get');
		Route::post('/filters','FilterController@save');

		Route::get('/showtutorial', 'TutorialController@show');
		Route::post('/hidetutorial', 'TutorialController@hide');


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
