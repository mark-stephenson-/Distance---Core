<?php

class Application extends BaseModel
{
    protected $table = 'apps';

    public static function current()
    {
        $appId = self::currentId();

        if ($appId) {
            $app = self::findOrFail($appId);
            return $app;
        } else {
            return $appId;
        }
    }

    public static function currentId()
    {
        if (Session::has('current-app')) {
            return Session::get('current-app');
        } else {
            $apps = self::allWithPermission();

            $current = count($apps) > 0 ? reset($apps) : null;

            if ($current) {
                Session::put('current-app', $current->id);

                return $current->id;
            } else {
                return 0;
            }
        }
    }

    public function collectionsWithPermission()
    {
        if (!Sentry::check()) return null;

        $collections = $this->collections;

        if (!Sentry::getUser()->hasAccess('cms.apps.' . $this->getAttribute('id') . '.collection-management')) {
            $collections = array_filter($collections->all(), function($collection) {
                return Sentry::getUser()->hasAccess('cms.apps.' . $this->getAttribute('id') . '.collections.' . $collection->id . '.*');
            });
        }

        return $collections;
    }

    public static function allWithPermission()
    {
        if (!Sentry::check()) return null;
        
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