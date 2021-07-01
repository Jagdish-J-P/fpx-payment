<?php

namespace JagdishJP\FpxPayment\Messages;

use JagdishJP\FpxPayment\Constant\Type;
use JagdishJP\FpxPayment\Contracts\Message as Contract;
use JagdishJP\FpxPayment\Traits\VerifyCertificate;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthorizationRequest extends Message implements Contract {
	use VerifyCertificate;

	/**
	 * Message code on the FPX side
	 */
	public const CODE = 'AR';


	/**
	 * Message Url
	 */
	public $url;


	public function __construct() {
		parent::__construct();

		$this->url = App::environment('production') ?
			Config::get('fpx.urls.production.auth_request') :
			Config::get('fpx.urls.uat.auth_request');
	}

	/**
	 * handle a message
	 *
	 * @param array $options
	 * @return mixed
	 */
	public function handle($options) {
		$data = Validator::make($options, [
			'flow' => ['required', Rule::in([Type::FLOW_B2C])],
			'reference_id' => 'required',
			'datetime' => 'nullable',
			'currency' => 'nullable',
			'product_description' => 'required',
			'amount' => 'required',
			'customer_name' => 'required',
			'customer_email' => 'required',
			'bank_id' => 'required',
		])->validate();


		$this->type = self::CODE;
		$this->flow = $data['flow'];
		$this->reference = $data['reference_id'];
		$this->timestamp = $data['datetime'] ?? date("YmdHis");
		$this->currency = $data['currency'] ?? $this->currency;
		$this->productDescription = $data['product_description'];
		$this->amount = $data['amount'];
		$this->buyerEmail = $data['customer_email'];
		$this->buyerName = $data['customer_name'];
		$this->targetBankId = $data['bank_id'];
		$this->checkSum = $this->getCheckSum($this->format());

		return $this;
	}


	/**
	 * Format data for checksum
	 * @return string
	 */
	public function format() {
		$list = collect([
			$this->buyerAccountNumber ?? '',
			$this->targetBankBranch ?? '',
			$this->targetBankId ?? '',
			$this->buyerEmail ?? '',
			$this->buyerIBAN ?? '',
			$this->buyerId ?? '',
			$this->buyerName ?? '',
			$this->buyerMakerName ?? '',
			$this->flow ?? '',
			$this->type ?? '',
			$this->productDescription ?? '',
			$this->bankCode ?? '',
			$this->exchangeId ?? '',
			$this->id ?? '',
			$this->sellerId ?? '',
			$this->reference ?? '',
			$this->timestamp ?? '',
			$this->amount ?? '',
			$this->currency ?? '',
			$this->version ?? '',
		]);

		return $list->join('|');
	}
}
