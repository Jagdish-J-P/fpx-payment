<?php

namespace JagdishJP\FpxPayment\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

class FpxPublish extends Command {

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'fpx:publish {force? : override existing files.}';

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
		$publishables = ['config', 'controller', 'assets', 'views'];

		$force = $this->argument('force');

		foreach ($publishables as $publishable) {

			if (!empty($force) && !Str::is($force, 'force')) {
				$this->error("Invalid Argument. syntax: php artisan fpx:publish force");
				return 0;
			}

			$parameters = ['--provider' => 'JagdishJP\FpxPayment\FpxPaymentServiceProvider', '--tag' => "fpx-{$publishable}"];

			if (Str::is($force, 'force'))
				$parameters['--force'] = null;

			$this->info("Publishing {$publishable} file.");

			Artisan::call("vendor:publish", $parameters);
		}

		Artisan::call("config:cache");

		$this->info('Publishing completed.');
	}
}
