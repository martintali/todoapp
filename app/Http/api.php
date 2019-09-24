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

Route::get('/', 'Task\TasksController@getAll');

Route::group(['prefix' => '/task'], function() {
    Route::post('/', 'Task\TasksController@createTask');

    Route::put('{id}', 'Task\TasksController@updateTask');

    Route::delete('{id}', 'Task\TasksController@deleteTask');
});