<?php

use JagdishJP\FpxPayment\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FPX\Controller;
use Monarobase\CountryList\CountryListFacade;
use JagdishJP\FpxPayment\Models\Bank;

$directPath = Config::get('fpx.direct_path');
$indirectPath = Config::get('fpx.indirect_path');

Route::get('fpx/initiate/payment/{initiated_from?}/{test?}', function ($initiated_from = 'web', $test = '') {

	$banks = Bank::all()->sortBy('name')->pluck('name', 'bank_id');

	return view('fpx-payment::payment', compact('banks', 'test', 'initiated_from'));
})->name('fpx.initiate.payment');

Route::get('fpx/csr/request', function () {
	$countries = CountryListFacade::getList('en');
	return view('fpx-payment::csr_request', compact('countries'));
})->name('fpx.csr.request');

Route::post('payment/fpx/auth', [PaymentController::class, 'handle'])->name('fpx.payment.auth.request');

Route::post($directPath, [Controller::class, 'webhook'])->name('fpx.payment.direct.callback');
Route::post($indirectPath, [Controller::class, 'callback'])->name('fpx.payment.indirect.callback');
