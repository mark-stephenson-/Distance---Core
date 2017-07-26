<?php

use Illuminate\Database\Migrations\Migration;

class ResourcePublicToggle extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('resources', function($t) {
            $t->boolean('public')->defaults(0);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('resources', function($t) {
            $t->removeColumn('public');
        });
	}

}