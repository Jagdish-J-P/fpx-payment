<?php

namespace JagdishJP\FpxPayment\Messages;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use JagdishJP\FpxPayment\Constant\Response;
use JagdishJP\FpxPayment\Models\FpxTransaction;
use JagdishJP\FpxPayment\Contracts\Message as Contract;
use JagdishJP\FpxPayment\Exceptions\InvalidCertificateException;

class AuthorizationConfirmation extends Message implements Contract
{


	/**
	 * Message code on the FPX side
	 */
	public const CODE = 'AC';

	public const STATUS_SUCCESS = 'succeeded';
	public const STATUS_FAILED = 'failed';
	public const STATUS_PENDING = 'pending';

	public const STATUS_SUCCESS_CODE = '00';
	public const STATUS_PENDING_CODE = '09';

	/**
	 * handle a message
	 *
	 * @param array $options
	 * @return mixed
	 */
	public function handle($options)
	{
		$this->targetBankBranch = @$options['fpx_buyerBankBranch'];
		$this->targetBankId = @$options['fpx_buyerBankId'];
		$this->buyerIBAN = @$options['fpx_buyerIban'];
		$this->buyerId = @$options['fpx_buyerId'];
		$this->buyerName = @$options['fpx_buyerName'];
		$this->creditResponseStatus = @$options['fpx_creditAuthCode'];
		$this->creditResponseNumber = @$options['fpx_creditAuthNo'];
		$this->debitResponseStatus = @$options['fpx_debitAuthCode'];
		$this->debitResponseNumber = @$options['fpx_debitAuthNo'];
		$this->foreignId = @$options['fpx_fpxTxnId'];
		$this->foreignTimestamp = @$options['fpx_fpxTxnTime'];
		$this->buyerMakerName = @$options['fpx_makerName'];
		$this->flow = @$options['fpx_msgToken'];
		$this->type = @$options['fpx_msgType'];
		$this->exchangeId = @$options['fpx_sellerExId'];
		$this->id = @$options['fpx_sellerExOrderNo'];
		$this->sellerId = @$options['fpx_sellerId'];
		$this->reference = @$options['fpx_sellerOrderNo'];
		$this->timestamp = @$options['fpx_sellerTxnTime'];
		$this->amount = @$options['fpx_txnAmount'];
		$this->currency = @$options['fpx_txnCurrency'];
		$this->checkSum = @$options['fpx_checkSum'];

		try {
			if (App::environment('production') || Config::get('fpx.should_verify_response'))
				$this->verifySign($this->checkSum, $this->format());

			$transaction = $this->saveTransaction();
			$this->responseFormat = $transaction->response_format;
			$this->additionalParams = $transaction->additional_params;

			if ($this->debitResponseStatus == self::STATUS_SUCCESS_CODE) {
				return [
					'status' => self::STATUS_SUCCESS,
					'message' => 'Payment is successfull',
					'transaction_id' => $this->foreignId,
					'reference_id' => $this->reference,
					'amount' => $this->amount,
					'transaction_timestamp' => $this->foreignTimestamp,
					'buyer_bank_name' => $this->targetBankBranch,
					'response_format' => $this->responseFormat,
					'additional_params' => $this->additionalParams,
				];
			}

			if ($this->debitResponseStatus == self::STATUS_PENDING_CODE) {
				return [
					'status' => self::STATUS_PENDING,
					'message' => 'Payment Transaction Pending',
					'transaction_id' => $this->foreignId,
					'reference_id' => $this->reference,
					'amount' => $this->amount,
					'transaction_timestamp' => $this->foreignTimestamp,
					'buyer_bank_name' => $this->targetBankBranch,
					'additional_params' => $this->additionalParams,
					'response_format' => $this->responseFormat,
				];
			}

			return [
				'status' => self::STATUS_FAILED,
				'message' => @Response::STATUS[$this->debitResponseStatus] ?? 'Payment Request Failed',
				'transaction_id' => $this->foreignId,
				'reference_id' => $this->reference,
				'amount' => $this->amount,
				'transaction_timestamp' => $this->foreignTimestamp,
				'buyer_bank_name' => $this->targetBankBranch,
				'additional_params' => $this->additionalParams,
				'response_format' => $this->responseFormat,
			];
		} catch (InvalidCertificateException $e) {
			return [
				'status' => self::STATUS_FAILED,
				'message' => "Failed to verify the request origin",
				'transaction_id' => $this->foreignId,
				'reference_id' => $this->reference,
				'amount' => $this->amount,
				'transaction_timestamp' => $this->foreignTimestamp,
				'buyer_bank_name' => $this->targetBankBranch,
				'additional_params' => $this->additionalParams,
				'response_format' => $this->responseFormat,
			];
		}
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
			'fpx_buyerBankBranch' => $this->targetBankBranch ?? '',
			'fpx_buyerBankId' => $this->targetBankId ?? '',
			'fpx_buyerIban' => $this->buyerIBAN ?? '',
			'fpx_buyerId' => $this->buyerId ?? '',
			'fpx_buyerName' => $this->buyerName ?? '',
			'fpx_creditAuthCode' => $this->creditResponseStatus ?? '',
			'fpx_creditAuthNo' => $this->creditResponseNumber ?? '',
			'fpx_debitAuthCode' => $this->debitResponseStatus ?? '',
			'fpx_debitAuthNo' => $this->debitResponseNumber ?? '',
			'fpx_fpxTxnId' => $this->foreignId ?? '',
			'fpx_fpxTxnTime' => $this->foreignTimestamp ?? '',
			'fpx_makerName' => $this->buyerMakerName ?? '',
			'fpx_msgToken' => $this->flow ?? '',
			'fpx_msgType' => $this->type ?? '',
			'fpx_sellerExId' => $this->exchangeId ?? '',
			'fpx_sellerExOrderNo' => $this->id ?? '',
			'fpx_sellerId' => $this->sellerId ?? '',
			'fpx_sellerOrderNo' => $this->reference ?? '',
			'fpx_sellerTxnTime' => $this->timestamp ?? '',
			'fpx_txnAmount' => $this->amount ?? '',
			'fpx_txnCurrency' => $this->currency ?? '',
		]);
	}

	/**
	 * Save response to transaction
	 *
	 * @return FpxTransaction
	 */
	public function saveTransaction(): FpxTransaction
	{
		$transaction = FpxTransaction::where(['unique_id' => $this->id])->firstOrNew();

		$transaction->reference_id = $this->reference;
		$transaction->request_payload = $transaction->request_payload ?? '';
		$transaction->response_format = $transaction->response_format ?? '';
		$transaction->additional_params = $transaction->additional_params ?? '';
		$transaction->unique_id = $this->id;
		$transaction->transaction_id = $this->foreignId;
		$transaction->debit_auth_code = $this->debitResponseStatus;
		$transaction->response_payload = $this->list()->toJson();
		$transaction->save();

		return $transaction;
	}
}
