<?php

namespace App\Http\Controllers\FPX;

use JagdishJP\FpxPayment\Http\Requests\AuthorizationConfirmation as Request;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Support\Facades\Cache;
use JagdishJP\FpxPayment\Fpx;

class Controller extends BaseController
{

	/**
	 * @param Request $request
	 * @return Response
	 */
	public function callback(Request $request)
	{
		$response = $request->handle();
		if ($response['response_format'] == 'JSON')
			return response()->json(['response' => $response, 'fpx_response' => $request->all()]);

		// Update your order status
	}

	/**
	 * @param Request $request
	 * @return string
	 */
	public function webhook(Request $request)
	{
		$response = $request->handle();

		// Update your order status

		return 'OK';
	}

	/**
	 * @param Request $request
	 * @return string
	 */
	public function initiatePayment(Request $request, $iniated_from = 'web', $test = '')
	{

		$banks = Cache::remember('banks', 1 * 24 * 60 * 60, function () {
			return Fpx::getBankList(true);
		});

		$response_format =	$iniated_from == 'app' ? 'JSON' : 'HTML';

		return view('fpx-payment::payment', compact('banks', 'response_format', 'test', 'request'));
	}
}
