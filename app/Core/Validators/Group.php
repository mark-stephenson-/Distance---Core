<?php
namespace Core\Validators;

class Group extends Validator
{
    public static $rules = array(
        'name'      => 'required',
    );

    public static $messages = array();
}