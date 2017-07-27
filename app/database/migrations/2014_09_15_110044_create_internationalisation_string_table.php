<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInternationalisationStringTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('i18n_strings', function(Blueprint $table)
		{
			$table->increments('id');
			
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->enum('status', array('draft', 'awaiting-approval', 'published', 'retired', 'for-review'))->default('draft');
            
			$table->integer('key');
            $table->string('lang');
            $table->string('value');
            
            $table->unique(array('key','lang'));
            
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
		Schema::drop('i18n_strings');
	}

}
