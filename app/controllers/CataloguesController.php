<?php

class CataloguesController extends BaseController
{
    

    public function index() {
        $catalogues = Catalogue::with('collections')->get();

        return View::make('catalogues.index', compact('catalogues'));
    }

    public function create() {
        $catalogue = new Catalogue;
        $collections = Collection::get();

        return View::make('catalogues.form', compact('catalogue', 'collections'));
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
        $catalogue->restrictions = array_filter(explode(',', trim(Input::get('restrictions', ''))));

        $catalogue->save();

        $catalogue->collections()->sync(Input::get('collections', []));

        return Redirect::route('catalogues.index')
                ->with('successes', new MessageBag(array($catalogue->name . ' has been created.')));
    }

    public function edit($catalogueId) {
        $catalogue = Catalogue::with('collections')->findOrFail($catalogueId);
        $collections = Collection::get();

        return View::make('catalogues.form', compact('catalogue', 'collections'));
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
        $catalogue->collections()->sync(Input::get('collections', []));

        $catalogue->save();


        return Redirect::route('catalogues.index')
                ->with('successes', new MessageBag(array($catalogue->name . ' has been updated.')));
    }

    public function destroy($catalogueId) {
        print $catalogueId;
    }

}