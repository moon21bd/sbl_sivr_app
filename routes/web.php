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

Route::middleware(['web', 'verify.login', 'check.logInfo'])->group(function () {
    Route::get('/send-otp', [\App\Http\Controllers\MainController::class, 'sendOtp'])->name('sendOtp');
    Route::get('/verify-otp', [\App\Http\Controllers\MainController::class, 'verifyOtp'])->name('verifyOtp');
});

Route::middleware(['web', 'verify.login'])->group(function () {
    // Your routes and route groups protected by the specified middleware go here
    Route::post('/tuj', [\App\Http\Controllers\UserJourneyController::class, 'track']);

    Route::get('/cards', [\App\Http\Controllers\MainController::class, 'cards'])->name('cards');
    Route::get('/account-and-loan', [\App\Http\Controllers\MainController::class, 'accountAndLoan'])->name('accountAndLoan');
    Route::get('/casasnd', [\App\Http\Controllers\MainController::class, 'casasnd'])->name('casasnd');
    Route::get('/account-dps', [\App\Http\Controllers\MainController::class, 'accountDPS'])->name('account-dps');
    Route::get('/fixed-deposit', [\App\Http\Controllers\MainController::class, 'fixedDeposit'])->name('fixed-deposit');
    Route::get('/loans-advances', [\App\Http\Controllers\MainController::class, 'loansAdvances'])->name('loans-advances');
    Route::get('/agent-banking', [\App\Http\Controllers\MainController::class, 'agentBanking'])->name('agent-banking');
    Route::get('/credit-card', [\App\Http\Controllers\MainController::class, 'creditCard'])->name('credit-card');

    Route::get('/debit-card', [\App\Http\Controllers\MainController::class, 'debitCard'])->name('debit-card');
    Route::get('/prepaid-card', [\App\Http\Controllers\MainController::class, 'prePaidCard'])->name('prepaid-card');
    Route::get('/esheba', [\App\Http\Controllers\MainController::class, 'eSheba'])->name('esheba');
    Route::get('/ewallet', [\App\Http\Controllers\MainController::class, 'eWallet'])->name('ewallet');
    Route::get('/islami-banking', [\App\Http\Controllers\MainController::class, 'islamiBanking'])->name('islami-banking');
    Route::get('/ib-account-related', [\App\Http\Controllers\MainController::class, 'ibAccountRelated'])->name('ib-account-related');
    Route::get('/ib-loans-advances', [\App\Http\Controllers\MainController::class, 'ibLoansAdvances'])->name('ib-loans-advances');
    Route::get('/sonali-products', [\App\Http\Controllers\MainController::class, 'sonaliBankProducts'])->name('sonali-products');
    Route::get('/spg', [\App\Http\Controllers\MainController::class, 'sonaliPaymentGateway'])->name('spg');

    Route::post('/otp-wrap', [\App\Http\Controllers\ApiController::class, 'sendOtpWrapper'])->name('sendOtpWrapper');
    Route::post('/verify-wrap', [\App\Http\Controllers\ApiController::class, 'verifyOtpWrapper'])->name('verifyOtpWrapper');
    Route::post('/upload-photo', [\App\Http\Controllers\MainController::class, 'uploadUserPhoto'])->name('verifyOtpWrapper');

    Route::post('/calldapi', [\App\Http\Controllers\ApiController::class, 'callDynamicApi'])->name('callDynamicApi');

    Route::post('/logout', 'AuthController@logout')->name('logout');

});

// web api routes
Route::get('/example', [\App\Http\Controllers\MainController::class, 'encryptWeb']);
Route::get('/get-balance', [\App\Http\Controllers\ApiController::class, 'getBalance']);





