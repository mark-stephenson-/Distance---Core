<?php

class Node extends BaseModel
{

    public function collection()
    {
        return $this->belongsTo('Collection');
    }

    public function nodetype()
    {
        return $this->belongsTo('NodeType', 'node_type');
    }

}