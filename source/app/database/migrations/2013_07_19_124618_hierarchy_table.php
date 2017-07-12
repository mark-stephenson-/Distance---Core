<?php

use Illuminate\Database\Migrations\Migration;

class HierarchyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hierarchies', function($table)  {

            $table->increments('id');

            $table->integer('node_id')->index();
            $table->integer('collection_id')->index();
            
            $table->integer('lft')->index();
            $table->integer('rgt')->index();
            $table->integer('tree')->index();

            $table->integer('created_by');
            $table->text('permissions')->nullable();

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
		Schema::drop('hierarchies');
	}

}