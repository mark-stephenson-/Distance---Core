<?php

class Collection extends BaseModel {

    protected $softDelete = true;
    protected $fillable = array('name', 'api_key', 'application_id', 'logo_id');

    public static function current()
    {
        $collectionId = self::currentId();

        if ($collectionId) {
            $collection = self::findOrFail($collectionId);
            return $collection;
        } else {
            return $collectionId;
        }
    }

    public static function currentId()
    {
        if (Session::has('current-collection')) {
            return Session::get('current-collection');
        } else {
            $collections = self::allWithPermission();

            $current = count($collections) > 0 ? reset($collections) : null;

            if ($current) {
                Session::put('current-collection', $current->id);

                return $current->id;
            } else {
                return 0;
            }
        }
    }

    public static function allWithPermission()
    {
        if (!Sentry::check()) return null;
        
        $collections = self::all();

        $collections = array_filter($collections->all(), function($collection) {
            return Sentry::getUser()->hasAccess('cms.apps.' . CORE_APP_ID . '.collections.' . $collection->id . '.*');
        });

        return $collections;
    }

    public function application()
    {
        return $this->belongsTo('Application');
    }

    public function hierarchy()
    {
        return $this->hasOne('Hierarchy');
    }

    public function catalogues()
    {
        return $this->hasMany('Catalogue');
    }

    public function nodes()
    {
        return $this->hasMany('Node');
    }

    public function nodetypes()
    {
        return $this->belongsToMany('NodeType');
    }

    public static function toDropDown()
    {
        $items = array();

        foreach(static::all() as $item) {
            $items[$item->id] = $item->name;
        }

        return $items;
    }

}