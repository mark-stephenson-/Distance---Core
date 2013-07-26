<?php
namespace Core\Validators;

class User extends Validator
{
    public static $rules = array(
        'first_name'    => 'required',
        'last_name'     => 'required',
        'email'         => 'required|email',
        'password'      => array(
            'required',
             'confirmed',
        ),
    );

    public static $messages = array(
        
    );

    public function __construct($attributes = null)
    {
        parent::__construct($attributes);

        if ($minLength = \Config::get('core.prefrences.password-min-length')) {
            self::$rules['password'][] = 'min:' . $minLength;
        }

        if ($passwordRegex = \Config::get('core.prefrences.password-regex')) {
            self::$rules['password'][] = 'regex:' . $passwordRegex;
            self::$messages['regex'] = \Config::get('core.prefrences.password-regex-failure');
        }
    }
}