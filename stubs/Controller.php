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
		$day = 1;
		$hour = 24;
		$minute = 60;
		$second = 60;

		$banks = Cache::remember('banks', $day * $hour * $minute * $second, function () {
			return Fpx::getBankList(true);
		});

		$response_format =	$iniated_from == 'app' ? 'JSON' : 'HTML';

		return view('fpx-payment::payment', compact('banks', 'response_format', 'test', 'request'));
	}
}
