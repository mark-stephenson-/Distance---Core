<?php
namespace Core\Validators;

class Catalogue extends Validator
{
    public static $rules = array(
        'name'         => 'required',
    );

    public static $messages = array();
}