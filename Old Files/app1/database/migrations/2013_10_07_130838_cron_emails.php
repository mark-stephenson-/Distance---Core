<?php

use Illuminate\Database\Migrations\Migration;

class CronEmails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('emails', function($table) {

            $table->increments('id');

            $table->string('key');
            $table->integer('associated_id');
            $table->string('email_address');

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
		Schema::drop('emails');
	}

}