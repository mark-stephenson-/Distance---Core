<?php

use Cartalyst\NestedSets\Nodes\EloquentNode;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Hierarchy extends EloquentNode {

    use SoftDeletingTrait;

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