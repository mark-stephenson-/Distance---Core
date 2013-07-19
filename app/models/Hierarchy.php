<?php

use Cartalyst\NestedSets\Nodes\EloquentNode;

class Hierarchy extends EloquentNode {

    protected $fillable = array('node_id', 'collection_id');

    protected $reservedAttributes = array(
        'left'  => 'lft',
        'right' => 'rgt',
        'tree'  => 'tree',
    );

}