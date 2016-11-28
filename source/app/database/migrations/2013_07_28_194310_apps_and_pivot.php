<?php

use Illuminate\Database\Migrations\Migration;

class AppsAndPivot extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('apps', function($table){
            $table->increments('id');

            $table->string('name');
            $table->string('api_key')->index();
            
            $table->timestamps();
        });

        Schema::create('app_collection', function($table){
            $table->increments('id');

            $table->integer('app_id');
            $table->integer('collection_id');
            
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
		Schema::drop('apps');
        Schema::drop('app_collection');
	}

}