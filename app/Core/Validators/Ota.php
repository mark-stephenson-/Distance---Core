<?php
namespace Core\Validators;

class Ota extends Validator
{
    public static $rules = array(
        'version'       => 'required',
        'platform'      => array('required', 'in:ios,android,windows'),
        'environment'   => array('required', 'in:testing,production'),
        'release_notes' => 'required',
        'application'          => 'required',
        'profile'          => 'required_if:platform,ios',
        'certificate'          => 'required_if:platform,android,windows'
    );

    public static $messages = array();
}
