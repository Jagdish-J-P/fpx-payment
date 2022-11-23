<?php

namespace JagdishJP\FpxPayment\Messages;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use JagdishJP\FpxPayment\Contracts\Message as Contract;

class BankEnquiry extends Message implements Contract {

	/**
	 * Message code on the FPX side
	 */
	public const CODE = 'BE';

	/**
	 * Message Url
	 */
	public $url;

	public function __construct() {
		parent::__construct();

		$this->type = self::CODE;
		$this->url = App::environment('production') ?
			Config::get('fpx.urls.production.bank_enquiry') :
			Config::get('fpx.urls.uat.bank_enquiry');
	}

	/**
	 * handle a message
	 *
	 * @param array $options
	 * @return mixed
	 */
	public function handle(array $options) {
		# code...
	}

	/**
	 * get request data from
	 *
	 */
	public function getData() {
		return collect([
			'fpx_msgType' => urlencode($this->type),
			'fpx_msgToken' => urlencode($this->flow),
			'fpx_sellerExId' => urlencode($this->exchangeId),
			'fpx_version' => urlencode($this->version),
			'fpx_checkSum' => $this->getCheckSum($this->format()),
		]);
	}

	/**
	 * connect and excute the request to FPX server
	 *
	 */
	public function connect(Collection $dataList)
	{
		$client = new Client();
		$response = $client->request('POST', $this->url, [
			'form_params' => $dataList->toArray()
		]);

		return Str::replaceArray("\n", [''], $response->getBody());
	}

	/**
	 * Parse the bank list response
	 *
	 */
	public function parseBanksList($response) {
		if ($response == 'ERROR' || !$response) {
			return false;
		}

		while ($response !== false) {
			list($key1, $value1) = explode("=", $response);
			$value1 = urldecode($value1);
			$response_value[$key1] = $value1;
			$response = strtok("&");
		}


		$data = $response_value['fpx_bankList'] . "|" .
						$response_value['fpx_msgToken'] . "|" .
						$response_value['fpx_msgType']  . "|" .
						$response_value['fpx_sellerExId'];

		$checksum = $response_value['fpx_checkSum'];

		if (App::environment('production') || Config::get('fpx.should_verify_response'))
		$this->verifySign($checksum, $data);

		$bankListToken = strtok($response_value['fpx_bankList'], ",");

		$i = 1;
		while ($bankListToken !== false) {
			list($key1, $value1) = explode("~", $bankListToken);
			$value1 = urldecode($value1);
			$bankList[$i  . ' - ' . $key1] = $value1;
			$i++;
			$bankListToken = strtok(",");
		}

		return $bankList;
	}


	/**
	 * Banks List
	 */
	public function getBanks($id = null) {
		$banks = collect([
			[
				"bank_id" => "ABB0233",
				"status" => "offline",
				"name" => "Affin Bank Berhad",
				"short_name" => "Affin Bank"
			],
			[
				"bank_id" => "ABMB0212",
				"status" => "offline",
				"name" => "Alliance Bank Malaysia Berhad",
				"short_name" => "Alliance Bank (Personal)"
			],
			[
				"bank_id" => "AMBB0209",
				"status" => "offline",
				"name" => "AmBank Malaysia Berhad",
				"short_name" => "AmBank"
			],
			[
				"bank_id" => "BIMB0340",
				"status" => "offline",
				"name" => "Bank Islam Malaysia Berhad",
				"short_name" => "Bank Islam"
			],
			[
				"bank_id" => "BMMB0341",
				"status" => "offline",
				"name" => "Bank Muamalat Malaysia Berhad",
				"short_name" => "Bank Muamalat "
			],
			[
				"bank_id" => "BKRM0602",
				"status" => "offline",
				"name" => "Bank Kerjasama Rakyat Malaysia Berhad ",
				"short_name" => "Bank Rakyat"
			],
			[
				"bank_id" => "BSN0601",
				"status" => "offline",
				"name" => "Bank Simpanan Nasional",
				"short_name" => "BSN"
			],
			[
				"bank_id" => "BCBB0235",
				"status" => "offline",
				"name" => "CIMB Bank Berhad",
				"short_name" => "CIMB Clicks"
			],
			[
				"bank_id" => "CIT0219",
				"status" => "offline",
				"name" => "CITIBANK BHD",
				"short_name" => "Citibank"
			],
			[
				"bank_id" => "HLB0224",
				"status" => "offline",
				"name" => "Hong Leong Bank Berhad",
				"short_name" => "Hong Leong Bank"
			],
			[
				"bank_id" => "HSBC0223",
				"status" => "offline",
				"name" => "HSBC Bank Malaysia Berhad",
				"short_name" => "HSBC Bank"
			],
			[
				"bank_id" => "KFH0346",
				"status" => "offline",
				"name" => "Kuwait Finance House (Malaysia) Berhad",
				"short_name" => "KFH"
			],
			[
				"bank_id" => "MBB0228",
				"status" => "offline",
				"name" => "Malayan Banking Berhad (M2E)",
				"short_name" => "Maybank2E"
			],
			[
				"bank_id" => "MB2U0227",
				"status" => "offline",
				"name" => "Malayan Banking Berhad (M2U)",
				"short_name" => "Maybank2U"
			],
			[
				"bank_id" => "OCBC0229",
				"status" => "offline",
				"name" => "OCBC Bank Malaysia Berhad",
				"short_name" => "OCBC Bank"
			],
			[
				"bank_id" => "PBB0233",
				"status" => "offline",
				"name" => "Public Bank Berhad",
				"short_name" => "Public Bank"
			],
			[
				"bank_id" => "RHB0218",
				"status" => "offline",
				"name" => "RHB Bank Berhad",
				"short_name" => "RHB Bank"
			],
			[
				"bank_id" => "SCB0216",
				"status" => "offline",
				"name" => "Standard Chartered Bank",
				"short_name" => "Standard Chartered"
			],
			[
				"bank_id" => "UOB0226",
				"status" => "offline",
				"name" => "United Overseas Bank",
				"short_name" => "UOB Bank"
			],
			[
				"bank_id" => "AGRO01",
				"status" => "offline",
				"name" => "BANK PERTANIAN MALAYSIA BERHAD (AGROBANK)",
				"short_name" => "AGRONet"
			],
      [
        "bank_id" => "BOCM01",
        "status" => "offline",
        "name" => "Bank Of China (M) Berhad",
        "short_name" => "Bank Of China (M) Berhad"
      ],
      [
        "bank_id" => "LOAD001",
        "status" => "offline",
        "name" => "LOAD001",
        "short_name" => "LOAD001"
      ]
		]);

		$banks = $banks->merge($this->getTestingBanks());

		if (is_null($id)) {
			return $banks;
		}

		return $banks->firstWhere('bank_id', $id);
	}

	public function getTestingBanks() {
		if (App::environment('production')) {
			return [];
		}

		return [
			[
				"bank_id" => "ABB0234",
				"status" => "offline",
				"name" => "Affin Bank Berhad B2C  - Test ID",
				"short_name" => "Affin B2C - Test ID"
			],
			[
				"bank_id" => "ABB0233",
				"status" => "offline",
				"name" => "Affin Bank Berhad",
				"short_name" => "Affin Bank"
			],
			[
				"bank_id" => "ABMB0212",
				"status" => "offline",
				"name" => "Alliance Bank Malaysia Berhad",
				"short_name" => "Alliance Bank (Personal)"
			],
			[
				"bank_id" => "AGRO01",
				"status" => "offline",
				"name" => "BANK PERTANIAN MALAYSIA BERHAD (AGROBANK)",
				"short_name" => "AGRONet"
			],
			[
				"bank_id" => "AMBB0209",
				"status" => "offline",
				"name" => "AmBank Malaysia Berhad",
				"short_name" => "AmBank"
			],
			[
				"bank_id" => "BIMB0340",
				"status" => "offline",
				"name" => "Bank Islam Malaysia Berhad",
				"short_name" => "Bank Islam"
			],
			[
				"bank_id" => "BMMB0341",
				"status" => "offline",
				"name" => "Bank Muamalat Malaysia Berhad",
				"short_name" => "Bank Muamalat "
			],
			[
				"bank_id" => "BKRM0602",
				"status" => "offline",
				"name" => "Bank Kerjasama Rakyat Malaysia Berhad ",
				"short_name" => "Bank Rakyat"
			],
			[
				"bank_id" => "BOCM01",
				"status" => "offline",
				"name" => "BANK OF CHINA (M) BERHAD",
				"short_name" => "Bank Of China"
			],
			[
				"bank_id" => "BSN0601",
				"status" => "offline",
				"name" => "Bank Simpanan Nasional",
				"short_name" => "BSN"
			],
			[
				"bank_id" => "BCBB0235",
				"status" => "offline",
				"name" => "CIMB Bank Berhad",
				"short_name" => "CIMB Clicks"
			],
			[
				"bank_id" => "CIT0219",
				"status" => "offline",
				"name" => "CITIBANK BHD",
				"short_name" => "Citibank"
			],
			[
				"bank_id" => "HLB0224",
				"status" => "offline",
				"name" => "Hong Leong Bank Berhad",
				"short_name" => "Hong Leong Bank"
			],
			[
				"bank_id" => "HSBC0223",
				"status" => "offline",
				"name" => "HSBC Bank Malaysia Berhad",
				"short_name" => "HSBC Bank"
			],
			[
				"bank_id" => "KFH0346",
				"status" => "offline",
				"name" => "Kuwait Finance House (Malaysia) Berhad",
				"short_name" => "KFH"
			],
			[
				"bank_id" => "MBB0228",
				"status" => "offline",
				"name" => "Malayan Banking Berhad (M2E)",
				"short_name" => "Maybank2E"
			],
			[
				"bank_id" => "MB2U0227",
				"status" => "offline",
				"name" => "Malayan Banking Berhad (M2U)",
				"short_name" => "Maybank2U"
			],
			[
				"bank_id" => "OCBC0229",
				"status" => "offline",
				"name" => "OCBC Bank Malaysia Berhad",
				"short_name" => "OCBC Bank"
			],
			[
				"bank_id" => "PBB0233",
				"status" => "offline",
				"name" => "Public Bank Berhad",
				"short_name" => "Public Bank"
			],
			[
				"bank_id" => "RHB0218",
				"status" => "offline",
				"name" => "RHB Bank Berhad",
				"short_name" => "RHB Bank"
			],
			[
				"bank_id" => "TEST0021",
				"status" => "offline",
				"name" => "SBI Bank A",
				"short_name" => "SBI Bank A"
			],
			[
				"bank_id" => "TEST0022",
				"status" => "offline",
				"name" => "SBI Bank B",
				"short_name" => "SBI Bank B"
			],
			[
				"bank_id" => "TEST0023",
				"status" => "offline",
				"name" => "SBI Bank C",
				"short_name" => "SBI Bank C"
			],
			[
				"bank_id" => "SCB0216",
				"status" => "offline",
				"name" => "Standard Chartered Bank",
				"short_name" => "Standard Chartered"
			],
			[
				"bank_id" => "UOB0226",
				"status" => "offline",
				"name" => "United Overseas Bank",
				"short_name" => "UOB Bank"
			],
			[
				"bank_id" => "UOB0229",
				"status" => "offline",
				"name" => "United Overseas Bank - B2C Test",
				"short_name" => "UOB Bank Test ID"
			]
		];
	}

	/**
	 * Format data for checksum
	 * @return string
	 */
	public function format() {
		$list = collect([
			$this->flow ?? '',
			$this->type ?? '',
			$this->exchangeId ?? '',
			$this->version ?? '',
		]);

		return $list->join('|');
	}
}
