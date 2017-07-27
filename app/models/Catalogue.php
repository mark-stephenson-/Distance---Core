<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Catalogue extends BaseModel {

    use SoftDeletingTrait;

    public static function forNodeTypeSelect($collectionId = null)
    {
        if ($collectionId) {
            return self::where('collection_id', '=', $collectionId)->lists('name', 'id');
        }
 
        $collections = Collection::lists('name', 'id');
        $catalogues = self::get();
        $return = array();
 
        foreach($catalogues as $catalogue) {
            $return[$catalogue->id] = "(" . $collections[$catalogue->collection_id] . ") " . $catalogue->name;
        }
 
        return $return;
    }
    
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