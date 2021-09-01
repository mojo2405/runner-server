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

Route::get('/admin', function () {
    $users = \App::call('App\Http\Controllers\UserController@getAllUsers');
    $sos = \App::call('App\Http\Controllers\UserController@getAllSos');
    return view('admin',['users'=>$users , 'sos'=>$sos]);
});

Route::get('/admin-track', function () {
    $users = \App::call('App\Http\Controllers\UserController@getAllUsers');
    return view('admin-track',['users'=>$users]);
});

Route::group(['middleware' => 'cors'], function () {

    Route::options('{path}', function () {
    })->where('path', '.+');

    Route::post('/test', 'UserController@test');
    Route::post('/register', 'UserController@registerUser');

    Route::post('/update-location','LocationController@update');

    Route::post('/sos','LocationController@sos');

    Route::get('/get-users-data','UserController@getAllUsersWithLastLocation');

    Route::get('/get-user-route','UserController@getRoute');
    Route::get('/search-user-id','UserController@searchUser');

    Route::get('/get-unseen-sos','UserController@getUnseenSOS');
    Route::get('/get-new-sos','UserController@getNewSos');

    Route::post('/truncate-all-tables','UserController@truncateTables');

Route::get('/downloadExcel/xlsx','ExcelController@export');
});