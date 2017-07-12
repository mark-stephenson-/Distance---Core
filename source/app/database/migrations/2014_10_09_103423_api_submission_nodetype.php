<?php

use Illuminate\Database\Migrations\Migration;

class ApiSubmissionNodetype extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        $user = Sentry::getUserProvider()->create(array(
            'email'    => 'hello+prase@thedistance.co.uk',
            'password' => 'netsells123',
            'first_name' => 'Prase',
            'last_name' => 'Submission',
            'permissions' => array(
                'superuser' => 0
            )
        ));

        $nodeType = new \NodeType;
        $nodeType->name = 'submission';
        $nodeType->label = 'Submission';
        $nodeType->columns = array('columns' => array(
            'category' => 'code',
            'label' => 'json',
            'syntax' => 'json',
            'description' => 'The JSON of a submission'   
        ));

        if (!$nodeType->save()) {
            return 'Node type for a submission could not be created.';
        }
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//Reverse user and nodetype creation
	}

}