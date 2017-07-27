<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddedInStandardReportColumnOnPraseRecordsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('prase_records', function(Blueprint $table)
		{
			$table->tinyInteger('added_on_standard_report')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('prase_records', function(Blueprint $table)
		{
			$table->dropColumn('added_on_standard_report');
		});
	}

}
