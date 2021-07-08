<?php

namespace JagdishJP\FpxPayment\Commands;

use Illuminate\Console\Command;

class FpxPublish extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'fpx:publish';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Publishes FPX publishable resources.';

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
		$this->info('Publishing Config file.');
		\Artisan::call("vendor:publish --provider='JagdishJP\FpxPayment\FpxPaymentServiceProvider' --tag=fpx-config");

		$this->info('Publishing Controller.');
		\Artisan::call("vendor:publish --provider='JagdishJP\FpxPayment\FpxPaymentServiceProvider' --tag=fpx-controller");

		$this->info('Publishing Assets.');
		\Artisan::call("vendor:publish --provider='JagdishJP\FpxPayment\FpxPaymentServiceProvider' --tag=fpx-assets");

		$this->info('Publishing Payment View.');
		\Artisan::call("vendor:publish --provider='JagdishJP\FpxPayment\FpxPaymentServiceProvider' --tag=fpx-views");

		$this->info('Publishing completed.');
	}
}
