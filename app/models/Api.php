<?php

class Api extends \BaseModel {
    public static function makeResponse($content, $root_node = null, $status = 200)
    {
        $contentType = Request::header('Content-Type');
        $unset = array(
            'nodetype',
            'owned_by',
            'created_by',
            'latest_revision',
            'published_revision',
            'collection_id',
            'status'
        );

        if ( ! Input::get('modifiedSince') ) {
            $unset[] = 'deleted_at';
            $unset[] = 'retired_at';
        }

        foreach ($unset as $u) {
            unset($content->{$u});

            foreach ( $content as &$c ) {
                unset($c->{$u});
            }
        }

        if ( $contentType == "text/xml" ) {
            $json = $content->toJSON();
            $format = new format;
            print htmlspecialchars_decode(html_entity_decode($format->factory($json, 'JSON')->to_xml()));
        } else if ( $contentType == "application/json" ) {
            return $content;
        } else {
            return Response::make('Content-Type not recognised.', 400);
        }
    }
}