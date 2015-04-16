<?php

use Illuminate\Database\Migrations\Migration;

class CreatePraseNotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('prase_notes', function ($table) {
            
            $table->increments('id');
            
            $table->text('text')->nullable();
            
            $table->integer('prase_record_id')->nullable();
            $table->integer('prase_question_id')->nullable();
            
            $table->string('ward_name')->nullable();
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
		Schema::drop('prase_notes');
	}

}