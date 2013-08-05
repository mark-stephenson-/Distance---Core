<?php

use Illuminate\Database\Migrations\Migration;

class AddingUserTokenColumns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function($table) {
			$table->string('key');
			$table->timestamp('last_accessed');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function($table) {
			$table->deleteColumn('key');
			$table->deleteColumn('last_accessed');
		});
	}

}