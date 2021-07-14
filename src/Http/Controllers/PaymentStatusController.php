<?php

namespace JagdishJP\FpxPayment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use JagdishJP\FpxPayment\Messages\AuthEnquiry;

class PaymentStatusController extends Controller {

	/**
	 * Initiate the request authorization message to FPX
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function handle(Request $request) {
		return view('fpx-payment::auth_enquiry', [
			'request' => (new AuthEnquiry)->handle($request->all()),
		]);
	}
}
