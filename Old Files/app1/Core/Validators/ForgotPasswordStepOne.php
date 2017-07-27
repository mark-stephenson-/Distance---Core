<?php
namespace Core\Validators;

class ForgotPasswordStepOne extends Validator
{
    public static $rules = array(
        'email'      => array('email', 'required')
    );

    public static $messages = array();
}