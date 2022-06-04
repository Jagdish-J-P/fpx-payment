<?php

namespace JagdishJP\FpxPayment\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use JagdishJP\FpxPayment\Fpx;
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
					$status[] = Fpx::getTransactionStatus($row['reference_id']);
				}
			} catch (\Exception $e) {
				$status[] = [
					'status' => 'failed',
					'message' => $e->getMessage(),
					'transaction_id' => null,
					'reference_id' => $row['reference_id'],
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
			$bar->finish();
			$this->newLine();
			$this->newLine();

			$this->table(collect(Arr::first($status))->keys()->toArray(), $status);
			$this->newLine();


		}
		else{
			$this->error("There is no Pending transactions.");
			logger("Transaction Status", [
				'message' => 'There is no Pending transactions.',
			]);
		}
	}
}
