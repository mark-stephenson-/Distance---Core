<?php

class PRQuestion extends BaseModel {
    
    protected $table = 'prase_questions';
    
    public function note()
    {
        return $this->hasOne('PRNote', 'prase_question_id');
    }
    
    public function concern()
    {
        return $this->hasOne('PRConcern', 'prase_question_id');
    }
    
    public function node()
    {
        return $this->belongsTo('Node', 'question_node_id');
    }
    
    public function answer()
    {
        return $this->belongsTo('Node', 'answer_node_id');
    }
    
}