<?php

namespace JagdishJP\FpxPayment;

use JagdishJP\FpxPayment\Commands\UpdateBankListCommand;
use JagdishJP\FpxPayment\Commands\FpxPublish;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use JagdishJP\FpxPayment\Commands\PaymentStatusCommand;

class FpxPaymentServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 */
	public function boot()
	{
		$this->configureRoutes();

		$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'fpx-payment');

		$this->configureComponents();

		$this->configurePublish();

		$this->registerEvents();
	}

	/**
	 * Register the application services.
	 */
	public function register()
	{
		// Automatically apply the package configuration
		$this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'fpx');
	}

	public function configureComponents()
	{
		Blade::component('fpx-payment::components.pay', 'fpx-payment');
	}

	public function configureRoutes()
	{
		Route::group([
			'middleware' => Config::get('fpx.middleware')
		], function () {
			$this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
		});

		Route::prefix('api')->group(
			function () {
				Route::group([
					'middleware' => 'api'
				], function () {
					$this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
				});
			}
		);
	}

	public function configurePublish()
	{
		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__ . '/../config/config.php' => config_path('fpx.php'),
			], 'fpx-config');

			$this->publishes([
				__DIR__ . '/../stubs/Controller.php' => app_path('Http/Controllers/FPX/Controller.php'),
			], 'fpx-controller');

			$this->publishes([
				__DIR__ . '/../public/assets' => public_path('assets/vendor/fpx-payment'),
			], 'fpx-assets');

			$this->publishes([
				__DIR__ . '/../resources/views/payment.blade.php' => resource_path('views/vendor/fpx-payment/payment.blade.php'),
			], 'fpx-views');

			$this->commands([
				UpdateBankListCommand::class,
				FpxPublish::class,
				PaymentStatusCommand::class
			]);
		}
	}

	public function registerEvents()
	{
		// Event::listen(
		// 	PodcastProcessed::class,
		// 	[SendPodcastNotification::class, 'handle']
		// );
	}
}
