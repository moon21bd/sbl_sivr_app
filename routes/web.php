<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;


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
Route::get('/', [\App\Http\Controllers\MainController::class, 'home'])->name('home');
Route::post('/change-locale', 'App\Http\Controllers\LocaleController@changeLocale')->name('changeLocale');
Route::get('/check-login', [\App\Http\Controllers\AuthController::class, 'checkLoginStatus']);

Route::middleware(['web', 'verify.login'])->group(function () {
    // Your routes and route groups protected by the specified middleware go here
    Route::post('/tuj', [\App\Http\Controllers\UserJourneyController::class, 'track']);

    Route::get('/send-otp', [\App\Http\Controllers\MainController::class, 'sendOtp'])->name('sendOtp');
    Route::get('/verify-otp', [\App\Http\Controllers\MainController::class, 'verifyOtp'])->name('verifyOtp');

    Route::post('/otp-wrap', [\App\Http\Controllers\ApiController::class, 'sendOtpWrapper'])->name('sendOtpWrapper');
    Route::post('/verify-wrap', [\App\Http\Controllers\ApiController::class, 'verifyOtpWrapper'])->name('verifyOtpWrapper');
    Route::post('/upload-photo', [\App\Http\Controllers\MainController::class, 'uploadUserPhoto'])->name('verifyOtpWrapper');

    Route::post('/calldapi', [\App\Http\Controllers\ApiController::class, 'callDynamicApi'])->name('callDynamicApi');

    Route::post('/logout', 'AuthController@logout')->name('logout');

});

Route::middleware('web')->group(function () {
    // Web routes goes here
});

// web api routes
Route::get('/example', [\App\Http\Controllers\MainController::class, 'encryptWeb']);
Route::get('/get-balance', [\App\Http\Controllers\ApiController::class, 'getBalance']);





