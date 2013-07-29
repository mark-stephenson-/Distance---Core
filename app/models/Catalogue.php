<?php

class Catalogue extends BaseModel {

    public function collections() {
        return $this->belongsToMany('Collection');
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