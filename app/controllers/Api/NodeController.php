<?php namespace Api;

use Node, Response;

class NodeController extends \BaseController {
    
    public function nodes()
    {

    }

    public function node($id)
    {
        $collection = \App::make('collection');
        $node = Node::whereId($id)->whereCollectionId($collection->id)->first();

        if ( ! $node ) {
            return Response::make('node not found', 404);
        }

        return $node;
    }
}