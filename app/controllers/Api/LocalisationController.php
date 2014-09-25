<?php namespace Api;

use Api, Node, Resource, Collection, I18nString;
use App, Response;

class LocalisationController extends \BaseController {

	public function localisations()
	{
		$responseCode = 200;
		
        if ( \Request::header('Collection-Token') === NULL ) {
            return \Response::make('Collection-Token must be specified for this call.', 400);
        }

        
        // Loop through all the published nodes in the current collection
        // Identify all the fields for the nodes where the category is i18n compliant (string or HTML)
        // for each field, get the ID of the string for subsequent lookup
        // lookup each string ID from the translations table, grouped by language
        
        $collection = \App::make('collection');
        $nodes = Node::whereCollectionId($collection->id)->isPublished()->get();
        
        $i18nStrings = array();
        $funty = array();
        foreach($nodes as $node) {
           	$nodeRevision = $node->fetchRevision(NULL);
			foreach($node->nodetype->columns as $column) {
	            if ($column->category == 'string-i18n'
	                     || $column->category == 'html-i18n') {
	            	$val = $nodeRevision->{$column->name};
	            	if($val) $i18nStrings[] = intval($val);
            	}
        	}
        }

		$lookups = array();
        if(count($i18nStrings) > 0) {
	        foreach(I18nString::whereIn('key', $i18nStrings)->get() as $translation){
	        	if(!array_key_exists($translation->lang, $lookups)){
		        	$lookups[$translation->lang] = array();
		        }
	        	$lookups[$translation->lang][$translation->key] = $translation->value;
	        }
        }
        
        return \Api::makeResponse($lookups, 'localisations', $responseCode);
	}

}
