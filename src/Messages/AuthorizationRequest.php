<?php

namespace JagdishJP\FpxPayment\Messages;

use JagdishJP\FpxPayment\Contracts\Message as Contract;
use JagdishJP\FpxPayment\Traits\VerifyCertificate;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use JagdishJP\FpxPayment\Models\FpxTransaction;

class AuthorizationRequest extends Message implements Contract
{
	use VerifyCertificate;

	/**
	 * Message code on the FPX side
	 */
	public const CODE = 'AR';


	/**
	 * Message Url
	 */
	public $url;


	public function __construct()
	{
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
	public function handle($options)
	{
		$data = Validator::make($options, [
			/* 'flow' => ['required', Rule::in([Type::FLOW_B2C])], */
			'reference_id' => 'required',
			'datetime' => 'nullable',
			'currency' => 'nullable',
			'response_format' => 'nullable',
			'remark' => 'nullable',
			'amount' => 'required',
			'customer_name' => 'required',
			'customer_email' => 'required',
			'bank_id' => 'required',
		])->validate();


		$this->type = self::CODE;
		//	$this->flow = $data['flow'];
		$this->reference = $data['reference_id'];
		$this->timestamp = $data['datetime'] ?? date("YmdHis");
		$this->currency = $data['currency'] ?? $this->currency;
		$this->productDescription = $data['remark'] ?? ' ';
		$this->amount = $data['amount'];
		$this->buyerEmail = $data['customer_email'];
		$this->buyerName = $data['customer_name'];
		$this->targetBankId = $data['bank_id'];
		$this->checkSum = $this->getCheckSum($this->format());
		$this->responseFormat = $data['response_format'] ?? 'HTML';

		$this->saveTransaction();

		return $this;
	}


	/**
	 * Format data for checksum
	 * @return string
	 */
	public function format()
	{
		return $this->list()->join('|');
	}

	/**
	 * returns collection of all fields
	 *
	 * @return collection
	 */
	public function list()
	{
		return collect([
			'buyerAccountNumber' => $this->buyerAccountNumber ?? '',
			'targetBankBranch' => $this->targetBankBranch ?? '',
			'targetBankId' => $this->targetBankId ?? '',
			'buyerEmail' => $this->buyerEmail ?? '',
			'buyerIBAN' => $this->buyerIBAN ?? '',
			'buyerId' => $this->buyerId ?? '',
			'buyerName' => $this->buyerName ?? '',
			'buyerMakerName' => $this->buyerMakerName ?? '',
			'flow' => $this->flow ?? '',
			'type' => $this->type ?? '',
			'productDescription' => $this->productDescription ?? '',
			'bankCode' => $this->bankCode ?? '',
			'exchangeId' => $this->exchangeId ?? '',
			'id' => $this->id ?? '',
			'sellerId' => $this->sellerId ?? '',
			'reference' => $this->reference ?? '',
			'timestamp' => $this->timestamp ?? '',
			'amount' => $this->amount ?? '',
			'currency' => $this->currency ?? '',
			'version' => $this->version ?? '',
		]);
	}

	/**
	 * Save request to transaction
	 */
	public function saveTransaction()
	{

		$transaction = new FpxTransaction;
		$transaction->unique_id = $this->id;
		$transaction->reference_id = $this->reference;
		$transaction->response_format = $this->responseFormat;
		$transaction->request_payload = $this->list()->toJson();
		$transaction->save();
	}
}
