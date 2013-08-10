<?php namespace Api;

use Api, Node;
use Response, Request, Input;

class NodeController extends \BaseController {
    
    public function nodes()
    {
        if ( Request::header('Collection-Token') === NULL ) {
            return Response::make('Collection-Token must be specified for this call.', 400);
        }

        $collection = \App::make('collection');
        $nodes = Node::whereCollectionId($collection->id)->isPublished();

        if ( Input::get('nodeType') ) {
            $nodes = $nodes->whereNodeType(Input::get('nodeType'));
        }

        if ( Input::get('modifiedSince') ) {
            $nodes = $nodes->where('updated_at', '>', date('Y-m-d H:i:s', Input::get('modifiedSince')) );
        }

        $nodes = $nodes->get();

        return Api::makeResponse($nodes, 'nodes');
    }

    public function node($id)
    {
        if ( Request::header('Collection-Token') === NULL ) {
            return Response::make('Collection-Token must be specified for this call.', 400);
        }

        $collection = \App::make('collection');
        $node = Node::whereId($id)->whereCollectionId($collection->id)->first();

        if ( ! $node ) {
            return Response::make('node not found', 404);
        }

        return Api::makeResponse($node, 'node');
    }
}