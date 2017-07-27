<?php

use Illuminate\Database\Migrations\Migration;

class AddingSoftDeleteToResources extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('resources', function($table) {
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