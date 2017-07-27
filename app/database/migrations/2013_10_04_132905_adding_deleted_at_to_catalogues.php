<?php

use Illuminate\Database\Migrations\Migration;

class AddingDeletedAtToCatalogues extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('catalogues', function($table) {
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
		Schema::table('catalogues', function($table) {
			$table->dropColumn('deleted_at');
		});
	}

}