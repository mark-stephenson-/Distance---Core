<?php
namespace Core\Validators;

class ForgotPassword extends Validator
{
    public static $rules = array(
        'email'      => array('email', 'required'),
        'password' => array('required', 'same:confirm_password'),
        'confirm_password' => 'required'
    );

    public static $messages = array();
}