<?php
namespace Core\Validators;

class ManageTrust extends Validator
{
    public static $rules = array(
        'name' => 'required',
    );

    public static $messages = array();
}