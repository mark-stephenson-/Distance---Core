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

        return Api::makeResponse($this->doExtended($collections), 'collections');
    }

    public function doExtended($collections)
    {
        foreach ( $collections as &$collection) {
            if ( $collection->logo_id ) {
                $collection->logo = \Resource::select('id', 'filename', 'mime', 'ext', 'sync', 'description')->find($collection->logo_id)->toArray();
                unset($collection->logo_id);
            }
        }

        return $collections;
    }
}