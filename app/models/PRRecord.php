<?php

class PRRecord extends BaseModel {
    
    protected $table = 'prase_records';
    
    public function concerns()
    {
        return $this->hasMany('PRConcern', 'prase_concern_id');
    }
    
    public function notes()
    {
        return $this->hasMany('PRNote', 'prase_note_id');
    }
    
    public function questions()
    {
        return $this->hasMany('PRQuestion', 'prase_record_id');
    }
    
    public function ward()
    {
        return $this->belongsTo('Node', 'ward_node_id');
    }
    
}