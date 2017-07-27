<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CatalogueTableAndCollectionPivot extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('catalogues', function(Blueprint $table)
		{
			$table->increments('id');

            $table->string('name');
            $table->text('restrictions')->nullable();

			$table->timestamps();
		});

        Schema::create('catalogue_collection', function(Blueprint $table) {

            $table->increments('id');

            $table->integer('collection_id');
            $table->integer('catalogue_id');

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
        Schema::drop('catalogues');
		Schema::drop('catalogue_collection');
	}

}
