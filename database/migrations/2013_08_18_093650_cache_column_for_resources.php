<?php

use Illuminate\Database\Migrations\Migration;

class CacheColumnForResources extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('resources', function($table) {

            $table->integer('collection_id');

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

            $table->dropColumn('collection_id');

        });
	}

}