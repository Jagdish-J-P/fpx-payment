<?php

use Illuminate\Http\Request;
use JagdishJP\FpxPayment\Fpx;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\FPX\Controller;
use Monarobase\CountryList\CountryListFacade;
use JagdishJP\FpxPayment\Http\Controllers\PaymentController;
use JagdishJP\FpxPayment\Http\Controllers\PaymentStatusController;


$directPath = Config::get('fpx.direct_path');
$indirectPath = Config::get('fpx.indirect_path');

Route::match(['get', 'post'], 'fpx/initiate/payment/{iniated_from?}/{test?}', function (Request $request, $iniated_from = 'web', $test = '') {

	$banks = Fpx::getBankList(true);
	$response_format =	$iniated_from == 'app' ? 'JSON' : 'HTML';

	return view('fpx-payment::payment', compact('banks', 'response_format', 'test', 'request'));
})->name('fpx.initiate.payment');

Route::get('fpx/payment/status/{iniated_from?}/{test?}', function (Request $request, $iniated_from = 'web', $test = '') {
	$response_format = $iniated_from == 'app' ? 'JSON' : 'HTML';
	return view('fpx-payment::payment_status', compact('test', 'response_format', 'request'));
})->name('fpx.initiate.payment.status');

Route::get('fpx/csr/request', function () {

	$countries = CountryListFacade::getList('en');

	return view('fpx-payment::csr_request', compact('countries'));
})->name('fpx.csr.request');

Route::post('fpx/payment/auth', [PaymentController::class, 'handle'])->name('fpx.payment.auth.request');
Route::post('fpx/payment/auth/enquiry', [PaymentStatusController::class, 'handle'])->name('fpx.payment.auth.enquiry');

Route::post($directPath, [Controller::class, 'webhook'])->name('fpx.payment.direct.callback');
Route::post($indirectPath, [Controller::class, 'callback'])->name('fpx.payment.indirect.callback');
