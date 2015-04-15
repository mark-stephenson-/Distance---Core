<?php

use Illuminate\Database\Migrations\Migration;

class CreatePraseQuestionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('prase_questions', function($table) {
            
            $table->increments('id');
            
            $table->integer('question_node_id');
            $table->integer('answer_node_id')->nullable();
            
            $table->integer('prase_record_id');
            $table->integer('prase_concern_id')->nullable();
            $table->integer('prase_note_id')->nullable();

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
		Schema::drop('prase_questions');
	}

}