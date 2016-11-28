<?php

class CollectionsController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($appId)
    {
        $app = Application::find($appId);
        $collections = $app->collectionsWithPermission();

        return View::make('collections.index', compact('collections'));
    }

    public function create($appId)
    {
        $collection = new Collection;
        $collection->app_id = $appId;

        return View::make('collections.form', compact('collection'));
    }

    public function store($appId)
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
        $collection->application_id = $appId;
        $collection->save();

        $collectionHierarchy = new Hierarchy(array('collection_id' => $collection->id));
        $collectionHierarchy->makeRoot();

        // We'll also make the resources folder
        mkdir(app_path() . '/../resources/' . $collection->id);

        return Redirect::route('collections.index', $appId)
                ->with('successes', new MessageBag(array($collection->name . ' has been created.')));
    }

    public function edit($appId, $collectionId)
    {
        $collection = Collection::find($collectionId);

        if (!$collection) {
            return Redirect::back()
                ->withErrors(new MessageBag(array('That collection could not be found.')));
        }

        return View::make('collections.form', compact('collection'));
    }

    public function update($appId, $collectionId)
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

        return Redirect::route('collections.index', $appId)
                ->with('successes', new MessageBag(array($collection->name . ' has been updated.')));
    }

    public function destroy($appId, $collectionId) {
        $collection = Collection::find($collectionId);

        if ( ! $collection ) {
            return Redirect::back()
                ->withErrors( new MessageBag( array('That collection could not be found.') ) );
        }

        $collection->delete();

        return Redirect::route('collections.index', $appId)
                ->with('successes', new MessageBag(array($collection->name . ' has been deleted.')));
    }

    public function createResourceArchive($appId, $collectionId)
    {
        Artisan::call('core:createResourceArchive', array('collection-id' => $collectionId));
    }
}