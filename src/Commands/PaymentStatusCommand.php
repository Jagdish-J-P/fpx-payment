<?php

namespace JagdishJP\FpxPayment\Commands;

use JagdishJP\FpxPayment\Models\Bank;
use Illuminate\Console\Command;
use JagdishJP\FpxPayment\Messages\AuthEnquiry;

class PaymentStatusCommand extends Command
{
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
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{
		$handler = new AuthEnquiry;
		$reference_id = $this->argument('reference_id');
		$handler->handle(['reference_id' => $reference_id]);

		$dataList = $handler->getData();
		try {
			$response = $handler->connect($dataList);
			$token = strtok($response, "&");

			$responseData = $handler->parseStatusResponse($token);

			if ($responseData === false) {
				$this->error('We could not find any data');
				return;
			}

			dd($responseData);
			$this->newLine();

			/* $bar = $this->output->createProgressBar(count($bankList));
			$bar->start();

			foreach ($bankList as $key => $status) {
				$bankId = explode(" - ", $key)[1];
				$bank = $handler->getBanks($bankId);

				Bank::updateOrCreate(['bank_id' => $bankId], [
					'status' => $status == 'A' ? 'Online' : 'Offline',
					'name' => $bank['name'],
					'short_name' => $bank['short_name']
				]);

				$bar->advance();
			}

			$bar->finish();
			 */
		} catch (InvalidCertificateException $e) {
			return [
				'status' => 'failed',
				'message' => "Failed to verify the request origin",
				'transaction_id' => null,
				'reference_id' => $reference_id,
			];
		} catch (\Exception $e) {
			logger("Bank Updating failed", [
				'message' => $e->getMessage(),
			]);
			$this->error("request failed due to " . $e->getMessage());
			throw $e;
		}
	}
}
