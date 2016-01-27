<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPmosIdToRecords extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('prase_records', function (Blueprint $table) {
            $table->integer('pmos_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('prase_records', function (Blueprint $table) {
            $table->dropColumn('pmos_id');
        });
    }
}
