<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHierarchyOnGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		return;
		Schema::table('groups', function(Blueprint $table)
		{
			$table->tinyInteger('hierarchy')->after('permissions')->default(1);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		return;
		Schema::table('groups', function(Blueprint $table)
		{
			$table->dropColumn('hierarchy');
		});
	}

}
