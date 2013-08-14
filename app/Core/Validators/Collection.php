<?php
namespace Core\Validators;

class Collection extends Validator
{
    public static $rules = array(
        'name'         => 'required',
        'api_key'      => 'required',
        'application_id' => 'required',
    );

    public static $messages = array();
}