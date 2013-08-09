<?php namespace Api;

use App, Request, Response;
use Collection;

class HierarchyController extends \BaseController {

    public function hierarchy()
    {
        if ( Request::header('Collection-Token') === NULL ) {
            return Response::make('Collection-Token must be specified for this call.', 400);
        }

        $collection = Collection::find(App::make('collection')->id);

        $branches = $collection->hierarchy;
        $branches->findChildren();

        return $branches;
    }
}