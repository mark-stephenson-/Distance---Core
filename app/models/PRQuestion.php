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
    
}