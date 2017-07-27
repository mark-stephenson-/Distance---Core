<?php

use Illuminate\Database\Migrations\Migration;

class NodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('nodes', function($table) {

            $table->increments('id');

            $table->integer('collection_id')->index();
            $table->string('title');

            $table->integer('created_by')->defaults(0);
            $table->integer('owned_by');

            $table->integer('node_type')->index();

            $table->integer('latest_revision');
            $table->integer('published_revision')->nullable();
            $table->enum('status', ['draft', 'published', 'retired'])->defaults('draft');

            $table->timestamp('published_at')->nullable();
            $table->timestamp('retired_at')->nullable();

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
		Schema::drop('nodes');
	}

}