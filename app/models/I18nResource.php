<?php

class I18nResource extends BaseModel
{
	public function resource(){
		return $this->belongsTo('resource');
	}

	public function path(){
        return route('resources.loadWithLang', array($this->resource->collection_id, $this->resource->filename, $this->lang));
	}
}
