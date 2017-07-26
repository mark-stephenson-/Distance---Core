<?php

use Illuminate\Database\Migrations\Migration;

class ConvertingM2mToO2mCataloguesCollections extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('catalogues', function($table) {

            $table->integer('collection_id');

        });

        Schema::drop('catalogue_collection');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('catalogue_collection', function($table) {

            $table->increments('id');

            $table->integer('collection_id');
            $table->integer('catalogue_id');

            $table->timestamps();

        });

        Schema::table('catalogues', function($table) {

            $table->dropColumn('collection_id');

        });
	}

}