<?php
namespace Core\Validators;

class Node extends Validator
{
    public static $rules = array(
        'title'         => 'required',
    );

    public static $messages = array();
}