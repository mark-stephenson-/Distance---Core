<?php

class Api extends \BaseModel {
    public static function makeResponse($content, $root_node = null, $status = 200)
    {
        $contentType = Request::header('Content-Type');
        $unset = array( 'nodetype', 'owned_by', 'created_by', 'latest_revision', 'published_revision', 'collection_id', 'status' );

        if ( ! Input::get('modifiedSince') ) {
            $unset[] = 'deleted_at';
            $unset[] = 'retired_at';
        }

        $remove = function($content) use (&$remove, $unset) {

            if ( is_array($content) ) {
                foreach( $unset as $_u ) {
                    unset($content[$_u]);
                }
            } else if ( is_object($content) ) {
                foreach( $unset as $_u ) {
                    unset($content->$_u);
                }
            }

            if ( is_array($content) || is_object($content) ) {
                foreach ($content as &$value) {
                    $value = $remove($value);
                }
            }

            return $content;
        };

        $content = $remove($content);

        if ( $contentType == "text/xml" ) {
            $nodeTypes = NodeType::all()->lists('name', 'id');

            $format = new format;

            if ( $root_node != "nodes") {
                if ( method_exists($content, 'toArray') ) {
                    return Response::make($format->factory($content->toArray(), null, $nodeTypes)->to_xml($content->toArray(), null, $root_node), 200, array('Content-Type' => 'text/xml'));
                } else {
                    $xml = $format->factory($content, null, $nodeTypes)->to_xml($content, null, $root_node);
                    return Response::make($xml, 200, array('Content-Type' => 'text/xml'));
                }
            } else {
                $xml = $format->factory($content, null, $nodeTypes)->to_xml($content, null, $root_node);

                $xml = str_replace(array('<nodes>', '</nodes>'), '', $xml);

                return Response::make($xml, 200, array('Content-Type' => 'text/xml'));
            }
        } else if ( $contentType == "application/json" ) {
            return Response::make($content->toJSON(), 200, array('Content-Type' => 'application/json'));
        } else {
            return Response::make('Content-Type not recognised.', 400);
        }
    }
}