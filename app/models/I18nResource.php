<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class I18nResource extends BaseModel
{
    use SoftDeletingTrait;
    
	public function resource(){
		return $this->belongsTo('resource');
	}

	public function path(){
        return route('resources.load', array($this->resource->catalogue_id, $this->lang, $this->resource->filename));
	}
}
