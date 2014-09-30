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
            $nodeType = \NodeType::find(Input::get('nodeType'));

            if ( ! $nodeType ) {
                return Response::make(null, 404);
            }

            $nodes = $nodes->whereNodeType(Input::get('nodeType'));
            
        } else if ( Input::get('name') ) {
            
            $nodeType = \NodeType::where('name', Input::get('name'))->first();
            
            if ( ! $nodeType ) {
                return Response::make(null, 404);
            }
            
            $nodes = $nodes->whereNodeType($nodeType->id);
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
    
    public function add()
    {
        if (Request::header('Collection-Token') === NULL) {
            return Response::make('Collection-Token must be specified for this call.', 400);
        }

        $collection = \App::make('collection');
        
        $request = Request::instance();
        $content = $request->getContent();
        
        if (!$content) {
            return Response::make('No content recieved', 400);
        }

        $nodeType = \NodeType::where('name', 'submission')->get()->first();
        
        if (!$nodeType)
        {
            // Node type doesn't exist yet, create it
            $nodeType = new \NodeType;
            $nodeType->name = 'submission';
            $nodeType->label = 'Submission';
            $nodeType->columns = array('5424335d11505' => array(
                'category' => 'code',
                'label' => 'json',
                'syntax' => 'json',
                'description' => 'The JSON of a submission'   
            ));

            if (!$nodeType->save()) {
                return Response::make('Node type for a submission could not be created.', 400);
            }
            
            $nodeType->collections()->sync(array($collection->id));

            if (!$nodeType->createTable()) {
                return Response::make('There was a problem creating the database table for this node type, your data has not been lost.', 400);
            }
        }
        
        $user = User::where('email', '=', 'hello+prase@thedistance.co.uk')->first();
        
        $node = new Node;
        $node->title = 'Submission'.(Node::where('node_type', $nodeType->id)->count() + 1);
        $node->owned_by = $user->id;
        $node->created_by = $user->id;
        $node->node_type = $nodeType->id;
        $node->collection_id = $collection->id;

        if (!$node->save()) {
            return Response::make('Node for submission could not be created.', 400);
        }
        
        $nodetypeContent = array("json" => Request::instance()->getContent());
        $nodeColumnErrors = $node->nodetype->checkRequiredColumns($nodetypeContent);
        
        $nodetypeContent = $nodeType->parseColumns($nodetypeContent, null, false);
        $nodetypeContent['node_id'] = $node->id;
        $nodetypeContent['status'] = "draft";
        $nodetypeContent['created_by'] = $nodetypeContent['updated_by'] = $user->id;
        $nodetypeContent['created_at'] = $nodetypeContent['updated_at'] = \DB::raw('NOW()');

        $nodeDraft = $node->createDraft($nodetypeContent);

        if (!$nodeDraft) {
            return Response::make('Draft node for submission could not be created.', 400);
        }
        
        $node->latest_revision = $nodeDraft;
        $node->status = 'draft';
        
        if ($node->save())
        {
            return Response::make(array('success' => true, 'error' => null), 201);
        }
        return Response::make(array('success' => false, 'error' => 'Node for submission could not be saved.'), 500);
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
                                $node->{$item->name} = '';
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
