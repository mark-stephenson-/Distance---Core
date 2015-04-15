<?php

use Illuminate\Database\Migrations\Migration;

class CreatePraseConcernsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('prase_concerns', function($table) {
            
            $table->increments('id');
            
            $table->integer('serious_answer');
            $table->integer('prevent_answer');
            
            $table->integer('prase_note_id');
            $table->integer('prase_record_id');
            $table->integer('prase_question_id');
            
            $table->string('ward_name');
            $table->integer('ward_node_id')->nullable();
            $table->integer('hospital_node_id');
            
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
		Schema::drop('prase_concerns');
	}

}