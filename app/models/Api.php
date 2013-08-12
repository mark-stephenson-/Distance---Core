<?php

class Api extends \BaseModel {
    public static function makeResponse($content, $root_node = null, $status = 200)
    {
        $contentType = Request::header('Content-Type');

        // Had to add this here, because before this it (weirdly) doesn't work
        unset($content->nodetype);

        // Unset other items we don't want to pass
        unset(
            $content->deleted_at, // If this has a value, this node shouldn't be displayed
            $content->owned_by, // This wasn't passed in the original API
            $content->created_by, // This wasn't passed in the original API
            $content->latest_revision, // Currently no version access via the API
            $content->published_revision, // Currently no version access via the API
            $content->retired_at, // If this has a value, this node shouldn't be displayed
            $content->collection_id,
            $content->status
        );

        if ( $contentType == "text/xml" ) {
            $json = $content->toJSON();
            $format = new format;
            print $format->factory($json, 'JSON')->to_xml();
        } else if ( $contentType == "application/json" ) {
            return $content;
        } else {
            return Response::make('Content-Type not recognised.', 400);
        }
    }
}