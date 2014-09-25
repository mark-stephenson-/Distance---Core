<?php

class I18nResource extends BaseModel
{
    protected $softDelete = true;
    
	public function resource(){
		return $this->belongsTo('resource');
	}

	public function path(){
        return route('resources.load', array($this->resource->catalogue_id, $this->lang, $this->resource->filename));
	}
}
