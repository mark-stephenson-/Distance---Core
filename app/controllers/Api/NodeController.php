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

        if ( Input::get('headersOnly') == NULL or Input::get('headersOnly') == "false" ) {
            foreach ( $nodes as &$node ) {
                $published_revision = $node->fetchRevision( $node->published_revision );

                foreach ($node->nodetype->columns as $item) {
                    if ( $item->category == "code" ) {
                        $node->{$item->name} = '<![CDATA[' . $published_revision->{$item->name} . ']]>';
                    } else {
                        $node->{$item->name} = $published_revision->{$item->name};
                    }
                }
            }
        }

        return Api::makeResponse($nodes, array('nodes','node_type_label'));
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

        $published_revision = $node->fetchRevision( $node->published_revision );

        foreach ($node->nodetype->columns as $item) {
             if ( $item->category == "code" ) {
                $node->{$item->name} = '<![CDATA[' . $published_revision->{$item->name} . ']]>';
            } else {
                $node->{$item->name} = $published_revision->{$item->name};
            }
        }

        return Api::makeResponse($node, $node->nodetype->name);
    }
}