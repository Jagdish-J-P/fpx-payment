<?php

use Illuminate\Support\Facades\Route;
use JagdishJP\FpxPayment\Fpx;

Route::get('fpx/transaction/status/{reference_id?}', function ($reference_id = '') {

	$response = Fpx::getTransactionStatus($reference_id);
	return $response;
})->name('fpx.transaction.status');
