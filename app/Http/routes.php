<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::model('user', 'App\User');
Route::model('message', 'App\Message');

Route::group(['middleware' => 'auth'], function(){
    Route::get('/', function(){
        return redirect('chat');
    });
    Route::get('chat', 'ChatController@index');
    Route::get('admin', ['middleware' => 'admin', 'uses' => 'AdminController@index']);
    Route::get('message/{message}/delete', ['middleware' => 'admin', 'uses' => 'AdminController@deleteMessage']);
    Route::get('loadHistory/{user}', 'ChatController@loadHistory');
});

Route::controllers([
    'auth' => 'Auth\AuthController',
]);
