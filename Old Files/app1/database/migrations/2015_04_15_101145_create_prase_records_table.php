<?php

use Illuminate\Database\Migrations\Migration;

class CreatePraseRecordsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('prase_records', function($table) {
            
            $table->increments('id');

            $table->string('ward_name')->nullable();
            $table->integer('ward_node_id')->nullable();
            $table->integer('hospital_node_id');
            
            $table->text('basic_data')->nullable();
            $table->text('incomplete_reason')->nullable();
            $table->integer('time_tracked')->nullable();
            $table->integer('time_spent_questionnaire')->nullable();
            $table->integer('time_spent_patient')->nullable();
            $table->string('user')->nullable();
            $table->string('language')->nullable();
            $table->timestamp('start_date');
            
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
		Schema::drop('prase_records');
	}

}