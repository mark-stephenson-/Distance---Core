<?php

use Illuminate\Database\Migrations\Migration;

class UserBioAndFields extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function($table) {

            $table->text('bio')->nullable();
            $table->integer('image_id')->nullable();

            $table->text('field_1')->nullable();
            $table->text('field_2')->nullable();
            $table->text('field_3')->nullable();
            $table->text('field_4')->nullable();
            $table->text('field_5')->nullable();

        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function($table) {

            $table->dropColumn('bio');
            $table->dropColumn('image_id');

            $table->dropColumn('field_1');
            $table->dropColumn('field_2');
            $table->dropColumn('field_3');
            $table->dropColumn('field_4');
            $table->dropColumn('field_5');

        });
	}

}