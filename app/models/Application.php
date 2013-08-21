<?php

class Application extends BaseModel
{
    protected $table = 'apps';

    public static function current()
    {
        if (Session::get('current-app')) {
            return Session::get('current-app');
        } else {
            $apps = self::allWithPermission();

            $current = count($apps) > 0 ? reset($apps) : null;
            Session::put('current-app', $current);

            return $current;
        }
    }

    public function collectionsWithPermission()
    {
        $collections = $this->collections;

        $collections = array_filter($collections->all(), function($collection) {
            return Sentry::getUser()->hasAccess('cms.collections.' . $collection->id . '.*');
        });

        return $collections;
    }

    public static function allWithPermission()
    {
        $apps = self::all();

        $apps = array_filter($apps->all(), function($app) {
            return Sentry::getUser()->hasAccess('cms.apps.' . $app->id . '.*');
        });

        return $apps;
    }

    public function collections() {
        return $this->hasMany('Collection');
    }
}