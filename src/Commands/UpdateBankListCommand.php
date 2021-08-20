<?php

namespace JagdishJP\FpxPayment\Commands;

use JagdishJP\FpxPayment\Messages\BankEnquiry;
use JagdishJP\FpxPayment\Models\Bank;
use Illuminate\Console\Command;

class UpdateBankListCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'fpx:banks';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update FPX banks List.';

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
		$handler = new BankEnquiry;

		$dataList = $handler->getData();

		try {
			$response = $handler->connect($dataList);
			$token = strtok($response, "&");
			$bankList = $handler->parseBanksList($token);

			if ($bankList === false) {
				$this->error('We could not find any data');
				return;
			}

			$bar = $this->output->createProgressBar(count($bankList));
			$bar->start();

			foreach ($bankList as $key => $status) {
				$bankId = explode(" - ", $key)[1];
				$bank = $handler->getBanks($bankId);
				if (empty($bank)) {
					logger("Bank Not Found: ", [$bankId]);
					continue;
				}
				Bank::updateOrCreate(['bank_id' => $bankId], [
					'status' => $status == 'A' ? 'Online' : 'Offline',
					'name' => $bank['name'],
					'short_name' => $bank['short_name']
				]);

				$bar->advance();
			}

			$bar->finish();
			$this->newLine();
		} catch (\Exception $e) {
			logger("Bank Updating failed", [
				'message' => $e->getMessage(),
			]);
			$this->error("request failed due to " . $e->getMessage());
			throw $e;
		}
	}
}
