<?php
namespace Core\Validators;

class Ota extends Validator
{
    public static $rules = array(
        'version'         => 'required',
        'platform'       => array('required', 'in:ios,android'),
        'environment' => array('required', 'in:testing,production'),
        'release_notes' => 'required',
        'file'  => 'required'
    );

    public static $messages = array();
}