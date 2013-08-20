<?php namespace Api;

use Api, Catalogue, Resource;
use App, Response;

class ResourceController extends \BaseController {
    public function resources()
    {
        $responseCode = 200;

        if ( \Input::get('catalogueID')) {
            $catalogues_ids = array( \Input::get('catalogueID') );
        } else {
            $collection_catalogues_query = App::make('collection')->catalogues()->get();
            $catalogues_ids = array();

            foreach ($collection_catalogues_query as $catalogue) {
                $catalogues_ids[] = $catalogue->id;
            }
        }

        if (count($catalogues_ids)) {
            $catalogues = Catalogue::whereIn('id', $catalogues_ids);
        } else {
            return Response::make('', 404);
        }

        if ( \Input::get('sync') ) {
            $catalogues = $catalogues->with(array('resources' => function($query) {
                $sync = false;

                if ( \Input::get('sync') == "true") {
                    $sync = 1;
                }

                $query->whereSync( $sync );
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

        $result = $catalogues->get();

        if (\Input::get('modifiedSince')) {
            $empty = true;
            foreach($result as $catalogue) {
                if (count($catalogue->resources) > 0) {
                    $empty = false;
                }
            }

            if ($empty) {
                $responseCode = 304;
            }
        }

        return Api::makeResponse($result, 'catalogues', $responseCode);
    }

    public function resource($id)
    {
        $resource = Resource::whereId($id)->first();

        if ( ! $resource ) {
            return Response::make('resource not found', 404);
        }

        return Resource::fetch($resource->collection_id, $resource->filename);

    }
}