<?php

class User extends Cartalyst\Sentry\Users\Eloquent\User
{

    public function __construct()
    {
        parent::__construct();

        $this->hidden[] = 'permissions';
    }    

    /*
        Overriding the original implementation so we can login multiple places
     */
    public function getPersistCode()
    {
        if (!$this->persist_code) {
            $this->persist_code = $this->getRandomString();

            // Our code got hashed
            $persistCode = $this->persist_code;

            $this->save();

            return $persistCode;
        }
        return $this->persist_code;
    }

    public function getFullNameAttribute()
    {
        // Check for temp users
        if (!$this->getAttribute('first_name') and !$this->getAttribute('last_name')) {
            return $this->getAttribute('email');
        }

        return $this->getAttribute('first_name') . ' ' . $this->getAttribute('last_name');
    }

    public function getAccessibleNodesAttribute($accessible_nodes)
    {
        if(empty($accessible_nodes)) {
            return array();
        }

        return json_decode($accessible_nodes);
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

    public static function forLookup($groupId) {

        if ((int) $groupId === 0) {
            return User::all();
        }

        $group = Group::findOrFail($groupId);
        return $group->users;
    }

    /**
     * Returns the top most level in the group hierarchy
     * users can only add lower level groups to other users
     *
     * @return integer
     */
    public function topMostGroupHierarchy() {
        if($this->hasAccess('superuser')) {
            return 0;
        }

        $topMostGroup = $this->groups->sortBy(function ($group) {
            return $group->hierarchy;
        })->first();

        // if user has no associated groups, just return a big number aka, last in the hierarchy
        if(empty($topMostGroup)) {
            return 100;
        }

        return (int) $topMostGroup->hierarchy;
    }
}
