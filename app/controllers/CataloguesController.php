<?php

class CataloguesController extends BaseController
{
    

    public function index() {
        $catalogues = Catalogue::whereCollectionId(Collection::current()->id)->get();
        
        return View::make('catalogues.index', compact('catalogues'));
    }

    public function create() {
        $catalogue = new Catalogue;

        return View::make('catalogues.form', compact('catalogue'));
    }

    public function store() {
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

        return Redirect::route('catalogues.index')
                ->with('successes', new MessageBag(array($catalogue->name . ' has been created.')));
    }

    public function edit($catalogueId) {
        $catalogue = Catalogue::findOrFail($catalogueId);

        return View::make('catalogues.form', compact('catalogue'));
    }

    public function update($catalogueId) {

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


        return Redirect::route('catalogues.index')
                ->with('successes', new MessageBag(array($catalogue->name . ' has been updated.')));
    }

    public function destroy($catalogueId) {
        print $catalogueId;
    }

}