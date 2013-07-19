<?php

use Illuminate\Database\Migrations\Migration;

class Users extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        $app = new Illuminate\Foundation\Artisan(App::make('app'));
        $app->call('migrate', array('--package' => 'cartalyst/sentry'));

        $user = Sentry::getUserProvider()->create(array(
            'email'    => 'core.admin@thedistance.co.uk',
            'password' => 'netsells123',
        ));

        $user->attemptActivation(null);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}