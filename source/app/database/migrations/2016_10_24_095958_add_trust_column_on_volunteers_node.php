<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrustColumnOnVolunteersNode extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('node_type_9', function(Blueprint $table)
		{
			$table->integer('trust')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('node_type_9', function(Blueprint $table)
		{
			$table->dropColumn('trust');
		});
	}

}
