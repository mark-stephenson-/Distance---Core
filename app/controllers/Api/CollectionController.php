<?php namespace Api;

use Api, Collection, Response;

class CollectionController extends \BaseController {

    public function collections()
    {
        $collections = Collection::all();

        // Remove any unneeded fields
        foreach ( $collections as &$collection ) {
            unset($collection['deleted_at'], $collection['created_at'], $collection['updated_at'], $collection['group_id']);
        }

        return Api::makeResponse($collections, 'collections');
    }
}