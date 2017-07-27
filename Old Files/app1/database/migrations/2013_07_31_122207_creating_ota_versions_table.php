<?php

use Illuminate\Database\Migrations\Migration;

class CreatingOtaVersionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ota_versions', function($table)
		{
			$table->increments('id');
		            $table->enum('platform', array('ios', 'android'));
		            $table->enum('environment', array('production', 'testing'));
		            $table->text('release_notes');
		            $table->string('version');
		            $table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ota_versions');
	}

}