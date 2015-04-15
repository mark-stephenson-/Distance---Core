<?php

class PRNote extends BaseModel {
    
    protected $table = 'prase_notes';
    
    public function record()
    {
        $this->belongsTo('PRRecord', 'prase_record_id');
    }
    
}