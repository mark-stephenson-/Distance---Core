<?php

class Api extends \BaseModel
{
    protected static $remove = array('nodetype', 'owned_by', 'created_by', 'latest_revision', 'published_revision', 'collection_id', 'status');
    protected static $convertDates = array('created_at', 'updated_at', 'published_at', 'retired_at', 'deleted_at');

    public static function makeResponse($content, $root_node = null, $status = 200)
    {
        // We want everything before the ;
        $contentTypeParts = explode(';', Request::header('Accept'));
        $contentType = $contentTypeParts[0];

        $content = self::cleanContent($content);

        if ($contentType == 'text/xml') {
            return self::makeXML($content, $root_node, $status);
        } elseif ($contentType == 'application/json') {
            return self::makeJSON($content, $root_node, $status);
        } else {
            return Response::make('Accept not recognised.', 400);
        }
    }

    protected static function cleanContent($content)
    {
        if (!Input::get('modifiedSince')) {
            $unset[] = 'deleted_at';
            $unset[] = 'retired_at';
        }

        if (is_array($content)) {
            foreach (self::$remove as $_u) {
                unset($content[$_u]);
            }

            foreach (self::$convertDates as $_u) {
                if (isset($content[$_u])) {
                    $content[$_u] = self::convertDate($content[$_u]);
                }
            }
        } elseif (is_object($content)) {
            foreach (self::$remove as $_u) {
                unset($content->$_u);
            }

            foreach (self::$convertDates as $_u) {
                if (isset($content->$_u)) {
                    $content->$_u = self::convertDate($content->$_u);
                }
            }
        }

        if (is_array($content) || is_object($content)) {
            foreach ($content as &$value) {
                $value = self::cleanContent($value);
            }
        }

        return $content;
    }

    public static function convertDate($date)
    {
        if ($date instanceof \Carbon\Carbon) {
            $newDate = $date->format('Y-m-d H:i:s');
        } else {
            $newDate = date("Y-m-d\TH:i:s\Z", strtotime($date));
            $newDate = $date;
        }

        return $newDate;
    }

    protected static function makeJSON($content, $root_node, $status)
    {
        return Response::make($content, $status, array('Content-Type' => 'application/json'));
    }

    protected static function makeXML($content, $root_node, $status)
    {
        $nodeTypes = NodeType::all()->lists('name', 'id');

        $format = new format();

        if ($root_node != 'nodes') {
            if (method_exists($content, 'toArray')) {
                return Response::make($format->factory($content->toArray(), null, $nodeTypes)->to_xml($content->toArray(), null, $root_node), $status, array('Content-Type' => 'text/xml'));
            } else {
                $xml = $format->factory($content, null, $nodeTypes)->to_xml($content, null, $root_node);

                return Response::make($xml, $status, array('Content-Type' => 'text/xml'));
            }
        } else {
            $xml = $format->factory($content, null, $nodeTypes)->to_xml($content, null, $root_node);

            // Need to remove the node <nodes> if it exists
            $xml = str_replace(array('<nodes>', '</nodes>'), '', $xml);

            return Response::make($xml, $status, array('Content-Type' => 'text/xml'));
        }
    }
}
