<?php

use Cartalyst\NestedSets\Nodes\EloquentNode;

class Hierarchy extends EloquentNode {

    protected $fillable = array('id', 'node_id', 'collection_id');

    protected $reservedAttributes = array(
        'left'  => 'lft',
        'right' => 'rgt',
        'tree'  => 'tree',
    );

    public function node() {
        return $this->belongsTo('Node');
    }

    public static function updateOrder($branches, Collection $collection) {
        $root = $collection->hierarchy;
        $root->mapTreeAndKeep($branches);
    }

}