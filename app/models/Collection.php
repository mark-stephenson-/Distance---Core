<?php

class Collection extends BaseModel {

    protected $fillable = array('name', 'api_key');

    public function hierarchy()
    {
        return $this->hasOne('Hierarchy');
    }

}