<?php

class CataloguesController extends BaseController
{
    

    public function index($appId, $collectionId) {
        $catalogues = Catalogue::whereCollectionId($collectionId)->get();
        
        return View::make('catalogues.index', compact('catalogues', 'appId', 'collectionId'));
    }

    public function create($appId, $collectionId) {
        $catalogue = new Catalogue;

        return View::make('catalogues.form', compact('catalogue', 'collectionId'));
    }

    public function store($appId, $collectionId) {
        // Let's run the validator
        $validator = new Core\Validators\Catalogue;

        // If the validator fails
        if ($validator->fails()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $catalogue = new Catalogue;

        $catalogue->name = Input::get('name');
        $catalogue->collection_id = Collection::current()->id;
        $catalogue->restrictions = array_filter(explode(',', trim(Input::get('restrictions', ''))));

        $catalogue->save();

        return Redirect::route('catalogues.index', array($appId, $collectionId))
                ->with('successes', new MessageBag(array($catalogue->name . ' has been created.')));
    }

    public function edit($appId, $collectionId, $catalogueId) {
        $catalogue = Catalogue::findOrFail($catalogueId);

        return View::make('catalogues.form', compact('catalogue', 'collectionId'));
    }

    public function update($appId, $collectionId, $catalogueId) {

        $catalogue = Catalogue::findOrFail($catalogueId);

        // Let's run the validator
        $validator = new Core\Validators\Catalogue;

        // If the validator fails
        if ($validator->fails()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $catalogue->name = Input::get('name');
        $catalogue->restrictions = array_filter(explode(',', trim(Input::get('restrictions', ''))));

        $catalogue->save();


        return Redirect::route('catalogues.index', array($appId, $collectionId))
                ->with('successes', new MessageBag(array($catalogue->name . ' has been updated.')));
    }

    public function destroy($appId, $collectionId, $catalogueId) {
        print $catalogueId;
    }

}