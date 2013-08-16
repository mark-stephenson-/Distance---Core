<?php

class User extends Cartalyst\Sentry\Users\Eloquent\User
{

    public function getFullNameAttribute()
    {
        // Check for temp users
        if (!$this->getAttribute('first_name') and !$this->getAttribute('last_name')) {
            return $this->getAttribute('email');
        }

        return $this->getAttribute('first_name') . ' ' . $this->getAttribute('last_name');
    }

    public function groups()
    {
        return $this->belongsToMany('Group', 'users_groups');
    }

    public function getKeyAttribute($value) {
        $this->timestamps = false;
        $this->last_accessed = date('Y-m-d H:i:s');
        $this->save();

        return $value;
    }

    public function collections() {
        if ( $this->hasAccess('superuser') ) {
            return Collection::get();
        } else {
            $collections = Collection::get()->lists('id');
            $canAccess = array();

            foreach ( $collections as $collection ) {
                if ( $this->hasAccess('cms.collections.' . $collection .'.*') ) {
                    $canAccess[] = $collection;
                }
            }

            return Collection::whereIn('id', $canAccess)->get();
        }
    }

}
