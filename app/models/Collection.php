<?php

class Collection extends BaseModel {

    protected $fillable = array('name', 'api_key');

    public function hierarchy()
    {
        return $this->hasOne('Hierarchy');
    }

    public function nodes()
    {
        return $this->hasMany('Node');
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