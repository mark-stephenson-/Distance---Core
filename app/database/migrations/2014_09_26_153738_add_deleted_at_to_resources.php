<?php

use Illuminate\Database\Migrations\Migration;

class AddDeletedAtToResources extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('i18n_resources', function($table) {

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

		Schema::table('i18n_resources', function($table) {
			$table->dropSoftDeletes();
		});
	}

}
