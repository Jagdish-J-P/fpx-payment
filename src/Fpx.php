<?php

namespace JagdishJP\FpxPayment;

use Exception;
use JagdishJP\FpxPayment\Messages\AuthEnquiry;
use JagdishJP\FpxPayment\Messages\BankEnquiry;
use JagdishJP\FpxPayment\Models\Bank;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use JagdishJP\FpxPayment\Exceptions\InvalidCertificateException;

class Fpx
{

	/**
	 * returns collection of bank_id and name
	 *
	 * @param bool $getLatest (optional) pass true to get latest banks
	 * @return \Illuminate\Support\Collection
	 */
	public static function getBankList(bool $getLatest = false)
	{
		if ($getLatest) {
			try {
				$bankEnquiry = new BankEnquiry;
				$dataList = $bankEnquiry->getData();
				$response = $bankEnquiry->connect($dataList);
				$token = strtok($response, "&");
				$bankList = $bankEnquiry->parseBanksList($token);

				if ($bankList === false) {
					throw new Exception('We could not find any data');
				}

				foreach ($bankList as $key => $status) {
					$bankId = explode(" - ", $key)[1];
					$bank = $bankEnquiry->getBanks($bankId);

					if (empty($bank)) {
						logger("Bank Not Found: ", [$bankId]);
						continue;
					}

					Bank::updateOrCreate(['bank_id' => $bankId], [
						'status' => $status == 'A' ? 'Online' : 'Offline',
						'name' => $bank['name'],
						'short_name' => $bank['short_name']
					]);
				}
			} catch (Exception $e) {
				throw $e;
			}
		}

		return Bank::all()->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->pluck('name', 'bank_id');
	}

	/**
	 * Returns status of transaction
	 *
	 * @param string $reference_id reference order id
	 * @return array
	 */
	public static function getTransactionStatus(string $reference_id)
	{
		try {
			$authEnquiry = new AuthEnquiry;
			$authEnquiry->handle(compact('reference_id'));

			$dataList = $authEnquiry->getData();
			$response = $authEnquiry->connect($dataList);

			$token = strtok($response, "&");

			$responseData = $authEnquiry->parseResponse($token);

			if ($responseData === false) {
				return [
					'status' => 'failed',
					'message' => 'We could not find any data',
					'transaction_id' => null,
					'reference_id' => $reference_id,
					'amount' => null,
					'transaction_timestamp' => null,
					'buyer_bank_name' => null,
					'response_format' => null,
					'additional_params' => null,
				];
			}
			return $responseData;
		} catch (ModelNotFoundException $e) {
			return [
				'status' => 'failed',
				'message' => 'Invalid reference Id',
				'transaction_id' => null,
				'reference_id' => $reference_id,
				'amount' => null,
				'transaction_timestamp' => null,
				'buyer_bank_name' => null,
				'response_format' => null,
				'additional_params' => null,
			];
		} catch (InvalidCertificateException $e) {
			return  [
				'status' => 'failed',
				'message' => "Failed to verify the request origin",
				'transaction_id' => null,
				'reference_id' => $reference_id,
				'amount' => null,
				'transaction_timestamp' => null,
				'buyer_bank_name' => null,
				'response_format' => null,
				'additional_params' => null,
			];
		} catch (\Exception $e) {
			return [
				'status' => 'failed',
				'message' => $e->getMessage(),
				'transaction_id' => null,
				'reference_id' => $reference_id,
				'amount' => null,
				'transaction_timestamp' => null,
				'buyer_bank_name' => null,
				'response_format' => null,
				'additional_params' => null,
			];

			logger("Transaction Status", [
				'message' => $e->getMessage(),
			]);
		}
	}
}
