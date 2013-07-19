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

    public function create()
    {
        if (!Sentry::getUser()->hasAccess('collections.create')) {
            die('no-access');
        }

        $collection = new Collection;

        return View::make('collections.form', compact('collection'));
    }

    public function store()
    {
        if (!Sentry::getUser()->hasAccess('collections.create')) {
            die('no-access');
        }

        // Let's run the validator
        $validator = new Core\Validators\Collection;

        // If the validator fails
        if ($validator->fails()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $collection = new Collection(Input::all());
        $collection->save();

        $collectionHierarchy = new Hierarchy(['collection_id' => $collection->id]);
        $collectionHierarchy->makeRoot();

        return Redirect::route('collections.index')
                ->with('successes', new MessageBag(array($collection->name . ' has been created.')));
    }

    public function edit($collectionId)
    {
        if (!Sentry::getUser()->hasAccess('collections.edit')) {
            die('no-access');
        }

        $collection = Collection::find($collectionId);

        if (!$collection) {
            return Redirect::back()
                ->withErrors(new MessageBag(array('That collection could not be found.')));
        }

        return View::make('collections.form', compact('collection'));
    }

    public function update($collectionId)
    {
        if (!Sentry::getUser()->hasAccess('collections.edit')) {
            die('no-access');
        }

        $collection = Collection::find($collectionId);

        if (!$collection) {
            return Redirect::back()
                ->withErrors(new MessageBag(array('That collection could not be found.')));
        }

        // Let's run the validator
        $validator = new Core\Validators\Collection;

        // If the validator fails
        if ($validator->fails()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $collection->fill(Input::all());
        $collection->save();

        return Redirect::route('collections.index')
                ->with('successes', new MessageBag(array($collection->name . ' has been updated.')));
    }
}