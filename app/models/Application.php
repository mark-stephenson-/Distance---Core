<?php

class Application extends BaseModel
{
    protected $table = 'apps';

    public function collections() {
        return $this->belongsToMany('Collection', 'app_collection', 'app_id');
    }
}