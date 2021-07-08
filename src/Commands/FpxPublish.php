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
		$publishables = ['config','controller','assets','views'];

		foreach($publishables as $publishable){

			$this->info("Publishing {$publishable} file.");
			\Artisan::call("vendor:publish",['--provider'=>'JagdishJP\FpxPayment\FpxPaymentServiceProvider','--tag'=> "fpx-{$publishable}"]);
		}

		$this->info('Publishing completed.');
	}
}
