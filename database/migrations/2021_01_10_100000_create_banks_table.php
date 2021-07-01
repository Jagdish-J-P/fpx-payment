<?php

use JagdishJP\FpxPayment\Models\Bank;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBanksTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('banks', function (Blueprint $table) {
			$table->id();
			$table->string('bank_id');
			$table->string('name');
			$table->string('short_name');
			$table->string('status')->default(Bank::STATUS_OFFLINE);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('banks');
	}
}
