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
Route::get('/send-otp', [\App\Http\Controllers\MainController::class, 'sendOtp'])->name('sendOtp');
Route::get('/verify-otp', [\App\Http\Controllers\MainController::class, 'verifyOtp'])->name('verifyOtp');

Route::post('/otp-wrap', [\App\Http\Controllers\ApiController::class, 'sendOtpWrapper'])->name('sendOtpWrapper');
Route::post('/verify-wrap', [\App\Http\Controllers\ApiController::class, 'verifyOtpWrapper'])->name('verifyOtpWrapper');

// web api routes
Route::get('/example', function () {
    return view('example');
});

Route::post('/change-locale', 'App\Http\Controllers\LocaleController@changeLocale')->name('changeLocale');



