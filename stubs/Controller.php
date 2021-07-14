<?php

namespace App\Http\Controllers\FPX;

use JagdishJP\FpxPayment\Http\Requests\AuthorizationConfirmation as Request;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController {

	/**
	 * @param Request $request
	 * @return Response
	 */
	public function callback(Request $request) {
		$response = $request->handle();
		if ($response['initiated_from'] == 'app')
		return response()->json(['response' => $response, 'fpx_response' => $request->all()]);

		// Update your order status
	}

	/**
	 * @param Request $request
	 * @return string
	 */
	public function webhook(Request $request) {
		$response = $request->handle();

		// Update your order status

		return 'OK';
	}
}
