<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInternationalisationResourcesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('i18n_resources', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('resource_id')->references('id')->on('resource');;
			$table->string('lang');
            $table->unique(array('resource_id','lang'));
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
		Schema::drop('i18n_resource');
	}

}
