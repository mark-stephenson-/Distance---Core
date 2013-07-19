<?php

use Illuminate\Database\Migrations\Migration;

class Collections extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('collections', function($table)  {

            $table->increments('id');

            $table->string('name');
            $table->string('api_key');
            $table->integer('logo_id');
            $table->integer('group_id')->nullable();

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
		Schema::drop('collections');
	}

}