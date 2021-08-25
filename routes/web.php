<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\FPX\Controller;
use Monarobase\CountryList\CountryListFacade;
use JagdishJP\FpxPayment\Http\Controllers\PaymentController;


$directPath = Config::get('fpx.direct_path');
$indirectPath = Config::get('fpx.indirect_path');

Route::post('fpx/payment/auth', [PaymentController::class, 'handle'])->name('fpx.payment.auth.request');

Route::post($directPath, [Controller::class, 'webhook'])->name('fpx.payment.direct.callback');
Route::post($indirectPath, [Controller::class, 'callback'])->name('fpx.payment.indirect.callback');

Route::match(
	['get', 'post'],
	'fpx/initiate/payment/{iniated_from?}/{test?}',
	[Controller::class, 'initiatePayment']
)->name('fpx.initiate.payment');

Route::get(
	'fpx/csr/request',
	function () {

		$countries = CountryListFacade::getList('en');
		return view('fpx-payment::csr_request', compact('countries'));
	}
)->name('fpx.csr.request');
