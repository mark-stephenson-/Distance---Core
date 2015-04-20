<?php

return array(

    'site_name' => 'The Core',

    'version' => '1.0.0',

    'emails' => array(
        'send_from' => 'core@thedistance.co.uk',
        'site_signature' => 'Regards, <br /> The Core',
    ),

    'labels' => array(
        'user_field_1' => null,
        'user_field_2' => null,
        'user_field_3' => null,
        'user_field_4' => null,
        'user_field_5' => null,
    ),

    'features' => array(
        'hierarchy' => true,
    ),

    'prefrences' => array(
        'preferred-node-view' => 'hierarchy',

        // Value is in MB
        'file-upload-limit' => '100',

        // Set Values to null to disable
        'password-min-length' => 8,
        'password-regex' => '/.*(?=.*\d)(?=.*[a-zA-Z]).*/',
        'password-regex-failure' => 'The password must contain at least one letter and one number',

        'default-catalogue-restrictions' => array('pdf', 'doc', 'docx', 'jpeg', 'jpg', 'png', 'gif'),
    ),

);
