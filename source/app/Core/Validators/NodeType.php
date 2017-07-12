<?php
namespace Core\Validators;

class NodeType extends Validator
{
    public static $rules = array(
        'label' =>  'required',
        'collections' =>  'required',
    );

    public static $messages = array();
}