<?php

use Illuminate\Database\Migrations\Migration;

class AddingAppIdToCollections extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('collections', function($table) {
			$table->integer('application_id')->after('group_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('collections', function($table) {
			$table->dropColumn('application_id');
		});
	}

}