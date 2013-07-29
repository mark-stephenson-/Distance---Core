<?php

class Resource extends BaseModel
{

    public function catalogue()
    {
        return $this->belongsTo('Catalogue');
    }

}