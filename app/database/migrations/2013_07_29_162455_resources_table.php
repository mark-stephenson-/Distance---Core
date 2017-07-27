<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ResourcesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('resources', function(Blueprint $table)
		{
			$table->increments('id');

            $table->string('filename');
            $table->string('mime');
            $table->string('ext');
            $table->integer('catalogue_id');
            $table->boolean('sync')->defaults(1);
            $table->text('description')->nullable();

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
		Schema::drop('resources');
	}

}
