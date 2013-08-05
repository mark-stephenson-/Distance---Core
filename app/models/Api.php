<?php

class Api extends \BaseModel {
    public static function makeResponse($content, $status = 200)
    {
        $contentType = Request::header('Content-Type');

        if ( $contentType == "text/xml" ) {
            return 'XML';
        } else if ( $contentType == "application/json" ) {
            $json = json_encode($content);

            if ( json_last_error() ) {
                return Response::make('JSON Encoding issue.', 400);  
            }

            return Response::make($json, $status)->header('Content-Type', 'application/json');
        } else {
            return Response::make('Content-Type not recognised.', 400);
        }
    }
}