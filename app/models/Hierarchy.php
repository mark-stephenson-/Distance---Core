<?php

class Hierarchy extends Cartalyst\NestedSets\Nodes\EloquentNode {

    protected $reservedAttributes = array(
        'left'  => 'lft',
        'right' => 'rgt',
        'tree'  => 'collection_id',
    );

}