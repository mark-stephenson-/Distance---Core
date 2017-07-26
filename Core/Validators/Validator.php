<?php
namespace Core\Validators;

abstract class Validator
{

    protected $attributes;
    public $errors;

    public function __construct($attributes = null)
    {
        $this->attributes = $attributes ?: \Input::all();
        $this->errors = new \MessageBag;
    }

    public function passes()
    {
        $validation = Validator::make($this->attributes, static::$rules, static::$messages);

        if ($validation->passes()) return true;

        $this->errors = $validation->messages();

        return false;
    }

    public function fails()
    {
        $validation = \Validator::make($this->attributes, static::$rules, static::$messages);

        if ($validation->passes()) return false;

        $this->errors = $validation->messages();

        return true;
    }

    public function messages()
    {
        return $this->errors;
    }

}