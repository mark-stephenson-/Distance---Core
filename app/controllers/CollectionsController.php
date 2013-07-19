<?php

class CollectionsController extends BaseController
{
    public function index()
    {

        if (!Sentry::getUser()->hasAccess('collections.index')) {
            die('no-access');
        }

        $collections = Collection::all();

        return View::make('collections.index', compact('collections'));
    }
}