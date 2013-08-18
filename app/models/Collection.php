<?php

class Collection extends BaseModel {

    protected $fillable = array('name', 'api_key', 'application_id', 'logo_id');

    public static function current()
    {
        if (Session::get('current-collection')) {
            return Session::get('current-collection');
        } else {
            $collections = self::allWithPermission();

            $current = count($collections) > 0 ? reset($collections) : null;
            Session::put('current-collection', $current);

            return $current;
        }
    }

    public static function allWithPermission()
    {
        $collections = self::all();

        $collections = array_filter($collections->all(), function($collection) {
            return Sentry::getUser()->hasAccess('cms.collections.' . $collection->id . '.*');
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
        $items = [];

        foreach(static::all() as $item) {
            $items[$item->id] = $item->name;
        }

        return $items;
    }

}