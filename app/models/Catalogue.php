<?php

class Catalogue extends BaseModel {

    protected $softDelete = true;
    
    public function collection() {
        return $this->belongsTo('Collection');
    }

    public function resources() {
        return $this->hasMany('Resource');
    }
    
    public function getRestrictionsAttribute($restrictions)
    {
        return ($restrictions) ? json_decode($restrictions) : array();
    }

    public function setRestrictionsAttribute($restrictions)
    {
        $restrictions = ($restrictions) ?: array();

        $this->attributes['restrictions'] = json_encode($restrictions);
    }

}