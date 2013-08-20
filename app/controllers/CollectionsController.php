<?php

class CollectionsController extends BaseController
{
    public function index()
    {
        $collections = Collection::allWithPermission();

        return View::make('collections.index', compact('collections'));
    }

    public function create()
    {
        $collection = new Collection;

        return View::make('collections.form', compact('collection'));
    }

    public function store()
    {
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

        $collectionHierarchy = new Hierarchy(array('collection_id' => $collection->id));
        $collectionHierarchy->makeRoot();

        // We'll also make the resources folder
        mkdir(app_path() . '/../resources/' . $collection->id);

        return Redirect::route('collections.index')
                ->with('successes', new MessageBag(array($collection->name . ' has been created.')));
    }

    public function edit($collectionId)
    {
        $collection = Collection::find($collectionId);

        if (!$collection) {
            return Redirect::back()
                ->withErrors(new MessageBag(array('That collection could not be found.')));
        }

        return View::make('collections.form', compact('collection'));
    }

    public function update($collectionId)
    {
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