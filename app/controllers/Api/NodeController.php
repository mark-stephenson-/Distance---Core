<?php namespace Api;

use Api, Node, Resource;
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

                if ( $node->published_revision ) {
                    $node = $this->doExtended($node);
                }
            }

            // Need to go through and sort the node types (to make this a bit easier later on)
            $return = array();

            foreach ($nodes->toArray() as $node) {
                $return[str_plural($node['nodetype']['name'])][] = $node;
            }
        } else {
            $return = $nodes->toArray();
        }

        return Api::makeResponse($return, 'nodes');
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

        if ( $node->published_revision ) {

            $node = $this->doExtended($node);

            return Api::makeResponse($node, $node->nodetype->name);
        } else {
            return Response::make('No published nodes', 404);
        }
    }

    private function doExtended($node) {
        $published_revision = $node->fetchRevision( $node->published_revision );

            foreach ($node->nodetype->columns as $item) {

                if ( Input::get('expandChildNodes') ) {
                        if ( $item->category == "resource" and ( isset($item->includeWhenExpanded) and $item->includeWhenExpanded) ) {
                            if ( $published_revision->{$item->name} ) {
                                $resource = @Resource::whereId( $published_revision->{$item->name})->first()->toArray();

                                unset($resource['catalogue_id'], $resource['created_at'], $resource['updated_at']);

                                $node->{$item->name} = $resource;
                            }
                        } else {
                            $node->{$item->name} = $published_revision->{$item->name};
                        }
                    } else {
                        $node->{$item->name} = $published_revision->{$item->name};
                    }
                    
            }

            return $node;
    }
}