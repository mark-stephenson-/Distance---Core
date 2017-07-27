<?php

use Illuminate\Database\Migrations\Migration;

class CreatingSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('settings', function($table)
		{
			$table->increments('id');
		            $table->string('name')->index();
		            $table->text('value');
		            $table->integer('user_id')->default(0);
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
		Schema::drop('settings');
	}

}