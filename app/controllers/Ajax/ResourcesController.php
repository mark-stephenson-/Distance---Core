<?php namespace Ajax;

use Input, Response;
use Resource;

class ResourcesController extends \BaseController
{
    public function toggleSync() {
        if ( Input::get('resourceID') !== null and Input::get('sync') !== null ) {
            $resource = Resource::whereId( Input::get('resourceID') )->first();

            $resource->sync = Input::get('sync');
            $resource->save();
        } else {
            return Response::make('', 400);
        }
    }
}