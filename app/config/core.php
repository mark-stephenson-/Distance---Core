<?php

return array(

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

        // Set Values to null to disable
        'password-min-length' => 8,
        'password-regex' => '/.*(?=.*\d)(?=.*[a-zA-Z]).*/',
        'password-regex-failure' => 'The password must contain at least one letter and one number',

        'default-catalogue-restrictions' => ['pdf', 'doc', 'docx', 'jpeg', 'jpg', 'png', 'gif'],
    ),

);