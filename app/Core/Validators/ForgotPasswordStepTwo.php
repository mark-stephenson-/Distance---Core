<?php
namespace Core\Validators;

class ForgotPasswordStepTwo extends Validator
{
    public static $rules = array(
        'password'      => array('required', 'same:confirm_password'),
        'confirm_password' => array('required')
    );

    public static $messages = array();
}