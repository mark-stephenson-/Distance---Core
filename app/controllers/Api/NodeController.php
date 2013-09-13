<?php namespace Api;

use Api, Node, Resource, User;
use Response, Request, Input;

class NodeController extends \BaseController {
    
    public function nodes($forAPI = true)
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
            $carbon = new \Carbon\Carbon(Input::get('modifiedSince'));
            $nodes = $nodes->where('updated_at', '>', $carbon->toDateTimeString() );
        }

        $nodes = $nodes->get();

        if ( Input::get('modifiedSince') and ( count($nodes) == 0) ) {
            return Response::make('', 304);
        }

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

        if ( $forAPI === true ) {
            return Api::makeResponse($return, 'nodes');
        } else {
            return $return;
        }
    }

    public function emailNode()
    {
        return Response::make('', 200);
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
                        } else if ( $item->category == "nodelookup-multi" and ( isset($item->includeWhenExpanded) and $item->includeWhenExpanded) ) {
                            $nodes = @Node::whereIn('id', explode(',', $published_revision->{$item->name}))->get();

                            foreach ($nodes as &$_node) {
                                if ($_node) {
                                    $_node = $this->doExtended($_node);
                                } else {
                                    $_node = '';
                                }
                            }

                            $node->{str_plural($item->name)} = $nodes->toArray();
                        } else if ( $item->category == "nodelookup" and ( isset($item->includeWhenExpanded) and $item->includeWhenExpanded) ) {
                            if ( $published_revision->{$item->name} ) {
                                $nodes = @Node::whereId( $published_revision->{$item->name})->first();
                                if ($nodes) {
                                    $node->{$item->name} = $this->doExtended($nodes)->toArray();
                                } else {
                                    $node->{$item->name} = '';
                                }
                            } else {
                                $node->{$item->name} = false;
                            }
                        } else if ( $item->category == "userlookup-multi" and ( isset($item->includeWhenExpanded) and $item->includeWhenExpanded) ) {
                            $users = @User::whereIn('id', explode(',', $published_revision->{$item->name}))->get();

                            foreach ($users as &$_user) {
                                $_user = $_user->toArray();
                            }

                            $node->{str_plural($item->name)} = $users->toArray();
                        } else if ( $item->category == "userlookup" and ( isset($item->includeWhenExpanded) and $item->includeWhenExpanded) ) {
                            $user = @User::whereId( $published_revision->{$item->name})->first();
                            $node->{$item->name} = $user->toArray();
                        } else {
                            $node->{$item->name} = $published_revision->{$item->name};
                        }
                    } else {
                        $node->{$item->name} = $published_revision->{$item->name};
                    }

                    if ($item->category == "date") {
                        $node->{$item->name} = Api::convertDate($published_revision->{$item->name});
                    }
                    
            }

            return $node;
    }
}