<?php

namespace JagdishJP\FpxPayment\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use JagdishJP\FpxPayment\Exceptions\InvalidCertificateException;
use JagdishJP\FpxPayment\Messages\AuthEnquiry;
use JagdishJP\FpxPayment\Models\FpxTransaction;

class PaymentStatusCommand extends Command {

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'fpx:payment-status {reference_id? : Order Reference Id}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'get status of payment.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle() {

		$reference_ids = $this->argument('reference_id');
		if ($reference_ids) {
			$reference_ids = explode(',', $reference_ids);
			$reference_ids = FpxTransaction::whereIn('reference_id', $reference_ids)->get('reference_id')->toArray();
		}
		else{
			$reference_ids = FpxTransaction::whereNull('debit_auth_code')->orWhere('debit_auth_code' , AuthEnquiry::STATUS_PENDING_CODE)->get('reference_id')->toArray();
		}

		if($reference_ids){
			try {
				$bar = $this->output->createProgressBar(count($reference_ids));
				$bar->start();
				foreach ($reference_ids as $row) {
					$handler = new AuthEnquiry;
					$handler->handle(['reference_id' => $row['reference_id']]);

					$dataList = $handler->getData();
					$response = $handler->connect($dataList);

					$token = strtok($response, "&");

					$responseData = $handler->parseResponse($token);

					$bar->advance();

					if ($responseData === false) {
						$status[] = [
							'status' => 'failed',
							'message' => 'We could not find any data',
							'transaction_id' => null,
							'reference_id' => $row['reference_id'],
						];
						continue;
					}

					$responseData['additional_params'] = $row['additional_params'];
					$status[] = $responseData;
				}

				$this->newLine();
				$this->newLine();

				$this->table(collect(Arr::first($status))->keys(), $status);
				$this->newLine();

				$bar->finish();
			} catch (InvalidCertificateException $e) {
				return [
					'status' => 'failed',
					'message' => "Failed to verify the request origin",
					'transaction_id' => null,
					'reference_id' => $row['reference_id'],
				];
			} catch (\Exception $e) {
				$this->error($e->getMessage());
				logger("Transaction Status", [
					'message' => $e->getMessage(),
				]);
			}
		}
		else{
			$this->error("There is no Pending transactions.");
			logger("Transaction Status", [
				'message' => 'There is no Pending transactions.',
			]);
		}
	}
}
