<?php
namespace Core\Validators;

class OtaPasswords extends Validator
{
    public static $rules = array(
        'production_password'   => 'required',
        'testing_password'         => 'required',
    );

    public static $messages = array();
}