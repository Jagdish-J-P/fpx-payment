<?php

namespace JagdishJP\FpxPayment\Messages;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use JagdishJP\FpxPayment\Constant\Response;
use JagdishJP\FpxPayment\Contracts\Message as Contract;
use JagdishJP\FpxPayment\Exceptions\InvalidCertificateException;
use JagdishJP\FpxPayment\Models\Transaction;

class AuthorizationConfirmation extends Message implements Contract {


	/**
	 * Message code on the FPX side
	 */
	public const CODE = 'AC';

	public const STATUS_SUCCESS = 'succeeded';
	public const STATUS_FAILED = 'failed';
	public const STATUS_PENDING = 'Pending';

	public const STATUS_SUCCESS_CODE = '00';
	public const STATUS_PENDING_CODE = '09';

	/**
	 * handle a message
	 *
	 * @param array $options
	 * @return mixed
	 */
	public function handle($options) {
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
		$this->makerName = @$options['fpx_makerName'];
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
			if(App::environment('production') || Config::get('fpx.should_verify_response'))
				$this->verifySign($this->checkSum, $this->format());

			$this->initiated_from = $this->saveTransaction();

			if ($this->debitResponseStatus == self::STATUS_SUCCESS_CODE) {
				return [
					'status' => self::STATUS_SUCCESS,
					'message' => 'Payment is successfull',
					'transaction_id' => $this->foreignId,
					'reference_id' => $this->reference,
					'initiated_from' => $this->initiated_from,
				];
			} elseif ($this->debitResponseStatus == self::STATUS_PENDING_CODE) {
				return [
					'status' => self::STATUS_PENDING,
					'message' => 'Payment Transaction Pending',
					'transaction_id' => $this->foreignId,
					'reference_id' => $this->reference,
					'initiated_from' => $this->initiated_from,
				];
			}

			return [
				'status' => self::STATUS_FAILED,
				'message' => @Response::STATUS[$this->debitResponseStatus] ?? 'Payment Request Failed',
				'transaction_id' => $this->foreignId,
				'reference_id' => $this->reference,
				'initiated_from' => $this->initiated_from,
			];
		} catch (InvalidCertificateException $e) {
			return [
				'status' => self::STATUS_FAILED,
				'message' => "Failed to verify the request origin",
				'transaction_id' => $this->foreignId,
				'reference_id' => $this->reference,
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
			'targetBankBranch' => $this->targetBankBranch ?? '',
			'targetBankId' => $this->targetBankId ?? '',
			'buyerIBAN' => $this->buyerIBAN ?? '',
			'buyerId' => $this->buyerId ?? '',
			'buyerName' => $this->buyerName ?? '',
			'creditResponseStatus' => $this->creditResponseStatus ?? '',
			'creditResponseNumber' => $this->creditResponseNumber ?? '',
			'debitResponseStatus' => $this->debitResponseStatus ?? '',
			'debitResponseNumber' => $this->debitResponseNumber ?? '',
			'foreignId' => $this->foreignId ?? '',
			'foreignTimestamp' => $this->foreignTimestamp ?? '',
			'makerName' => $this->makerName ?? '',
			'flow' => $this->flow ?? '',
			'type' => $this->type ?? '',
			'exchangeId' => $this->exchangeId ?? '',
			'id' => $this->id ?? '',
			'sellerId' => $this->sellerId ?? '',
			'reference' => $this->reference ?? '',
			'timestamp' => $this->timestamp ?? '',
			'amount' => $this->amount ?? '',
			'currency' => $this->currency ?? '',
		]);
	}

	/**
	 * Save response to transaction
	 *
	 * @return string initiated from
	 */
	public function saveTransaction()
	{
		$transaction = Transaction::where(['unique_id'=>$this->id])->first();

		$transaction->transaction_id = $this->foreignId;
		$transaction->debit_auth_code = $this->debitResponseStatus;
		$transaction->response_payload = $this->list()->toJson();
		$transaction->save();

		return $transaction->initiated_from;
	}
}
