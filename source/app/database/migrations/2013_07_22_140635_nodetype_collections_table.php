<?php

use Illuminate\Database\Migrations\Migration;

class NodetypeCollectionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('collection_node_type', function($table) {

            $table->increments('id');

            $table->integer('collection_id');
            $table->integer('node_type_id');

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
		Schema::drop('collection_node_type');
	}

}