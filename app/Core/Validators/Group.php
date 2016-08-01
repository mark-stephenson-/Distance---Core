<?php
namespace Core\Validators;

class Group extends Validator
{
    public static $rules = array(
        'name'      => 'required',
        'hierarchy' => 'required|integer|min:1'
    );

    public static $messages = array();
}