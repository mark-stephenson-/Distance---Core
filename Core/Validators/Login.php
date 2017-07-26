<?php
namespace Core\Validators;

class Login extends Validator
{
    public static $rules = array(
        'email'         => 'required|email',
        'password'      => 'required',
    );

    public static $messages = array();
}