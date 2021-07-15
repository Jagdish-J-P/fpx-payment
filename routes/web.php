<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FPX\Controller;
use JagdishJP\FpxPayment\Fpx;
use JagdishJP\FpxPayment\Http\Controllers\PaymentController;
use JagdishJP\FpxPayment\Http\Controllers\PaymentStatusController;
use Monarobase\CountryList\CountryListFacade;


$directPath = Config::get('fpx.direct_path');
$indirectPath = Config::get('fpx.indirect_path');

Route::get('fpx/initiate/payment/{initiated_from?}/{test?}', function ($initiated_from = 'web', $test = '') {
	$banks = Fpx::getBankList();
	$banks = Fpx::getBankList(true);
    $data = ['referenceId' => uniqid(), 'amount' => '1.00', 'customerEmail' => 'jagdish1230@gmail.com',
    'customerName' => 'test', 'datetime' => null, 'desc' => 'test', 'title' => 'Submit and Pay', 'banks' => $banks,'test'=>$test,'initiated_from'=> $initiated_from];
	return view('fpx-payment::payment', $data);
})->name('fpx.initiate.payment');

Route::get('fpx/payment/status/{initiated_from?}/{test?}', function ($initiated_from = 'web', $test = '') {
	return view('fpx-payment::payment_status', compact('test', 'initiated_from'));
})->name('fpx.initiate.payment.status');

Route::get('fpx/csr/request', function () {
	$countries = CountryListFacade::getList('en');
	return view('fpx-payment::csr_request', compact('countries'));
})->name('fpx.csr.request');

Route::post('payment/fpx/auth', [PaymentController::class, 'handle'])->name('fpx.payment.auth.request');
Route::post('payment/fpx/auth/enquiry', [PaymentStatusController::class, 'handle'])->name('fpx.payment.auth.enquiry');

Route::post($directPath, [Controller::class, 'webhook'])->name('fpx.payment.direct.callback');
Route::post($indirectPath, [Controller::class, 'callback'])->name('fpx.payment.indirect.callback');
