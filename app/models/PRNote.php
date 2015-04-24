<?php

class PRNote extends BaseModel {
    
    protected $table = 'prase_notes';
    
    public function record()
    {
        return $this->belongsTo('PRRecord', 'prase_record_id');
    }
    
    public function hospital()
    {
        return $this->belongsTo('Node', 'hospital_node_id');
    }
    
    public function ward()
    {
        return $this->belongsTo('Node', 'ward_node_id');
    }
    
    public function question()
    {
        return $this->belongsTo('PRQuestion', 'prase_question_id');
    }
    
}