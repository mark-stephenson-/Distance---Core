<?php

class PRConcern extends BaseModel {
    
    /**
     *  @var string
     */
    protected $table = 'prase_concerns';
    
    /**
     *  @var array
     */
    protected $fillable = ['serious_answer', 'prevent_answer'];
    
    /**
     *  @return Relation
     */
    public function record()
    {
        return $this->belongsTo('PRRecord', 'prase_record_id');
    }
    
    /**
     *  @return Relation
     */
    public function question()
    {
        return $this->belongsTo('PRQuestion', 'prase_question_id');
    }
    
    /**
     *  @return Relation
     */
    public function note()
    {
        return $this->belongsTo('PRNote', 'prase_note_id');
    }
    
    /**
     *  @return array | Relation
     */
    public function ward()
    {
        if ($this->ward_node_id == null) {
            return [
                'name' => $this->ward_name,
                'hospital' => $this->hospital
            ];
        } else {
            return $this->belongsTo('Node', 'ward_node_id');
        }
    }
    
    /**
     *  @return Relation
     */
    public function hospital()
    {
        return $this->belongsTo('Node', 'hospital_node_id');
    }    
    
}