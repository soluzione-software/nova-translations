<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::get('/locales', 'LocalesController@get');
Route::post('/locales', 'LocalesController@store');
Route::delete('/locales/{locale}', 'LocalesController@delete');
Route::post('/locales/{locale}/import', 'LocalesController@import');
Route::get('/locales/{locale}/export', 'LocalesController@export');
Route::post('/locales/{locale}/sync', 'LocalesController@sync');

Route::get('/translations/{locale}', 'TranslationsController@get');
Route::post('/translations/{locale}', 'TranslationsController@store');
Route::put('/translations/{locale}', 'TranslationsController@update');
Route::delete('/translations', 'TranslationsController@delete');
