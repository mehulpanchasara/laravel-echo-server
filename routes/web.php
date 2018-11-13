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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'chat', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'ChatController@chatView');
    Route::get('/list', 'ChatController@getMessages');
    Route::post('/send', 'ChatController@sendMessage')->name('send-message');
    Route::post('/load-more','ChatController@loadMore')->name('load-more');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
