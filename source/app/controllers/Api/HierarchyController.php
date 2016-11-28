<?php

namespace Api;

use App;
use Input;
use Request;
use Response;
use Api;
use Collection;

class HierarchyController extends \BaseController
{
    public function hierarchy()
    {
        if (Request::header('Collection-Token') === null) {
            return Response::make('Collection-Token must be specified for this call.', 400);
        }

        Input::replace(array_merge(array(
            'headersOnly' => 'yes',
        ), Input::all()));

        $nodeData = App::make('Api\NodeController')->nodes(false);
        $nodes = array();

        foreach ($nodeData as $node) {
            $nodes[$node['id']] = $node;
        }

        $collection = Collection::find(App::make('collection')->id);

        $branches = $collection->hierarchy;
        $branches->findChildren();

        $return = $this->buildHierarchyLevel($branches, $nodes);

        return Api::makeResponse($return, 'hierarchy');
    }

    protected function buildHierarchyLevel($branches, $nodeData)
    {
        $return = array();

        foreach ($branches->getChildren() as $branch) {
            $return[] = array_merge($nodeData[$branch->node_id], array('branches' => $this->buildHierarchyLevel($branch, $nodeData)));
        }

        return $return;
    }
}
