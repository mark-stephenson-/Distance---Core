<?php

class Group extends Cartalyst\Sentry\Groups\Eloquent\Group
{

    public function users()
    {
        return $this->belongsToMany('User', 'users_groups');
    }

}