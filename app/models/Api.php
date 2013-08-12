<?php

class Api extends \BaseModel {
    public static function makeResponse($content, $root_node = null, $status = 200)
    {
        $contentType = Request::header('Content-Type');

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