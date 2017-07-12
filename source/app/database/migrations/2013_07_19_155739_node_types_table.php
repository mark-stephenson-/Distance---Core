<?php

use Illuminate\Database\Migrations\Migration;

class NodeTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('node_types', function($table) {

            $table->increments('id');

            $table->string('label');
            $table->string('name');
            $table->text('columns')->nullable();

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
		Schema::drop('node_types');
	}

}