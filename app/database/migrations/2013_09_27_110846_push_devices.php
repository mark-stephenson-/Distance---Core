<?php

use Illuminate\Database\Migrations\Migration;

class PushDevices extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_devices', function($table) {

            $table->create();

            $table->increments('id');

            $table->integer('user_id');
            $table->string('device_type');
            $table->string('device_token');

            $table->softDeletes();
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
		Schema::drop('user_devices');
	}

}