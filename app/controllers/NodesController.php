<?php

class NodesController extends BaseController
{

    public function hierarchy($collectionId = 0) {

        if (!Sentry::getUser()->hasAccess('')) {
            die('no-access');
        }

        $collection = Collection::find($collectionId);

        if (!$collection) {
            return Redirect::back()
                ->withErrors(['That collection could not be found.']);
        }

        Session::put('current-collection', $collection);
        Session::put('collection-node-view', 'hierarchy');

        $branches = $collection->hierarchy;
        $branches->findChildren();

        return View::make('nodes.hierarchy', compact('collection', 'branches'));
    }

}