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

/*
* Just to verify the db connection
*/
Route::get('/example', 'DBController@');
//Delete this ^

Route::get('/login','PageController@login');
Route::get('/register','PageController@register');

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/mycontent','ContentController@display');


//Temporary
Route::post('/login','PageController@login');
Route::post('/register','PageController@register');
Route::post('/mycontent','ContentController@display');



/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});
