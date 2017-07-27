<?php
namespace Core\Validators;

class ManageWard extends Validator
{
    public static $rules = array(
        'name' => 'required',
    );

    public static $messages = array();
}