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
            
            return $content;
        };

        $content = $remove($content);

        if ( $contentType == "text/xml" ) {
            $nodeTypes = NodeType::all()->lists('name', 'id');

            $format = new format;

            if ( $root_node != "nodes") {
                return Response::make($format->factory($content->toArray(), null, $nodeTypes)->to_xml($content->toArray(), null, $root_node), 200, array('Content-Type' => 'text/xml'));
            } else {
                $xml = $format->factory($content, null, $nodeTypes)->to_xml($content, null, $root_node);

                $xml = str_replace(array('<nodes>', '</nodes>'), '', $xml);

                return Response::make($xml, 200, array('Content-Type' => 'text/xml'));
            }
            // return Response::make(self::makeXML($content->toArray()), 200, array('Content-Type' => 'text/xml'));
        } else if ( $contentType == "application/json" ) {
            return Response::make($content->toJSON(), 200, array('Content-Type' => 'application/json'));
        } else {
            return Response::make('Content-Type not recognised.', 400);
        }
    }

    private static function makeXML($content) {
        // Create a DOMDocument
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $root = new DOMElement('xml');
        $dom->appendChild($root);

        foreach ( $content as $key => $value ) {
            if (is_numeric($key)) {
                // make string key...
                $key = 'item';
            }

            if ( is_array($value) || is_object($value) ) {
                $node = new DOMElement($key);
                $root->appendChild($node);

                $root->appendChild( self::makeXMLChild($value, $node) );
            }
        }

        return $dom->saveXML();
    }

    private static function makeXMLChild($data, $node, $root = 'item') {
        foreach ($data as $key => $value) {
            $node->{$key} = $value;
        }

        var_dump($node);
        return $node;
    }
}