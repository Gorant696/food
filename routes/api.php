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

Route::apiResource('foods', 'FoodsController');

Route::get('foods', 'FoodsController@index')->middleware('validate_index');

Route::post('foods', 'FoodsController@store')->middleware('validate_index');

Route::delete('foods', 'FoodsController@delete')->middleware('validate_index');

Route::put('foods', 'FoodsController@restore')->middleware('validate_index');