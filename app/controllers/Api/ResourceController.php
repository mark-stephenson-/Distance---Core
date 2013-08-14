<?php namespace Api;

use Api, Catalogue, Resource;
use App, Response;

class ResourceController extends \BaseController {
    public function resources()
    {
        $collection_catalogues_query = App::make('collection')->catalogues()->get();
        $catalogues_ids = array();

        foreach ($collection_catalogues_query as $catalogue) {
            $catalogues_ids[] = $catalogue->id;
        }

        $catalogues = Catalogue::whereIn('id', $catalogues_ids);

        if ( \Input::get('sync') ) {
            $catalogues = $catalogues->with(array('resources' => function($query) {
                $query->whereSync( \Input::get('sync') );
            }));
        } else {
            $catalogues = $catalogues->with('resources');
        }

        if ( \Input::get('modifiedSince') ) {
            $catalogues = $catalogues->with( array('resources' => function($query) {
                $query->withTrashed();
                $query->where('updated_at', '>', date('Y-m-d H:i:s', \Input::get('modifiedSince')) );
            }));
        }

        return Api::makeResponse($catalogues->get());
    }

    public function resource($id)
    {
        $resource = Resource::whereId($id)->first();

        if ( ! $resource ) {
            return Response::make('resource not found', 404);
        }

        return Resource::fetch($resource->id . '_id');

    }
}