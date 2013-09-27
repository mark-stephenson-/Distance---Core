<?php

use Illuminate\Database\Migrations\Migration;

class AddingDeletedAtColumnToApps extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('apps', function($table) {
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('resources', function($table) {
			$table->dropColumn('deleted_at');
		});
	}

}