<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\MainController::class, 'home'])->name('home');

Route::get('/check-login', [\App\Http\Controllers\AuthController::class, 'checkLoginStatus']);

Route::get('/getac', [\App\Http\Controllers\ApiController::class, 'getSavedAccountInfo']);

Route::middleware(['web', 'verify.login', 'check.logInfo'])->group(function () {
    Route::get('/send-otp', [\App\Http\Controllers\MainController::class, 'sendOtp'])->name('sendOtp');
    Route::get('/verify-otp', [\App\Http\Controllers\MainController::class, 'verifyOtp'])->name('verifyOtp');
    Route::post('/save', [\App\Http\Controllers\ApiController::class, 'saveAccountInfo']);
});

Route::middleware(['web'])->group(function () {
    Route::post('/calldapi', [\App\Http\Controllers\ApiController::class, 'callDynamicApi'])->name('callDynamicApi');
    Route::post('/resend', [\App\Http\Controllers\ApiController::class, 'resendOtp'])->name('resendOtp');
    Route::post('/change-locale', 'App\Http\Controllers\LocaleController@changeLocale')->name('changeLocale');
});

Route::middleware(['web', 'verify.login'])->group(function () {
    Route::post('/otp-wrap', [\App\Http\Controllers\ApiController::class, 'sendOtpWrapper'])->name('sendOtpWrapper');
    Route::post('/verify-wrap', [\App\Http\Controllers\ApiController::class, 'verifyOtpWrapper'])->name('verifyOtpWrapper');
});

Route::post('/tuj', [\App\Http\Controllers\UserJourneyController::class, 'track']);

Route::middleware(['web', 'verify.login', 'check.wallet.access'])->group(function () {
    Route::get('/ewallet', [\App\Http\Controllers\MainController::class, 'eWallet'])->name('ewallet');
});

Route::middleware(['web', 'verify.login'])->group(function () {

    Route::get('/get-balance', [\App\Http\Controllers\ApiController::class, 'getBalance']);
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

    Route::get('/islami-banking', [\App\Http\Controllers\MainController::class, 'islamiBanking'])->name('islami-banking');
    Route::get('/ib-account-related', [\App\Http\Controllers\MainController::class, 'ibAccountRelated'])->name('ib-account-related');
    Route::get('/ib-loans-advances', [\App\Http\Controllers\MainController::class, 'ibLoansAdvances'])->name('ib-loans-advances');
    Route::get('/sonali-products', [\App\Http\Controllers\MainController::class, 'sonaliBankProducts'])->name('sonali-products');
    Route::get('/spg', [\App\Http\Controllers\MainController::class, 'sonaliPaymentGateway'])->name('spg');

    Route::post('/upload-photo', [\App\Http\Controllers\MainController::class, 'uploadUserPhoto'])->name('uploadUserPhoto');

    Route::post('/logout', 'AuthController@logout')->name('logout');
    Route::post('/logout-on-close', 'AuthController@logoutOnClose')->name('logoutOnClose');

});

// web api routes
Route::get('/example', [\App\Http\Controllers\MainController::class, 'encryptWeb']);






