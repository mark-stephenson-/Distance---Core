<?php namespace Api;

use Request, Response;

class NodeTypeController extends \BaseController {
    
    public function nodeTypes()
    {
        if ( Request::header('Collection-Token') === NULL ) {
            return Response::make('Collection-Token must be specified for this call.', 400);
        }

        return \App::make('collection')->nodetypes;
    }
}