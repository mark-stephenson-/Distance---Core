<?php
namespace Core\Validators;

class Volunteer extends Validator
{
    public static $rules = array(
        'password'  => 'required',
        'firstname' => 'required',
        'lastname'  => 'required',
        'trust'  => 'required',
    );

    public static $messages = array();

    public function usernameRequired() {
        self::$rules['username'][] = 'required';

        return $this;
    }
}