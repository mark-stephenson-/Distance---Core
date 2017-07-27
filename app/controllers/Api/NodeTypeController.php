<?php namespace Api;

use App, Request, Response;
use Api;

class NodeTypeController extends \BaseController {
    
    public function nodeTypes()
    {
        if ( Request::header('Collection-Token') === NULL ) {
            return Response::make('Collection-Token must be specified for this call.', 400);
        }

        $nodetypes = App::make('collection')->nodetypes;

        return Api::makeResponse($nodetypes, 'node-types');
    }
}