<?php
namespace Core\Validators;

class ManageHospital extends Validator
{
    public static $rules = array(
        'name' => 'required',
    );

    public static $messages = array();
}