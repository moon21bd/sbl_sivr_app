<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

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

Route::post('/tuj', [\App\Http\Controllers\UserJourneyController::class, 'track']);

// Route::get('/', [\App\Http\Controllers\MainController::class, 'index']);
Route::get('/', [\App\Http\Controllers\MainController::class, 'home'])->name('home');

// web api routes
Route::get('/example', function () {
    return view('example');
});

Route::post('/change-locale', 'App\Http\Controllers\LocaleController@changeLocale')->name('changeLocale');



