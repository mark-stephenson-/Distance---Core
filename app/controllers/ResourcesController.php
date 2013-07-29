<?php

class ResourcesController extends BaseController
{
    
    public function load($fileName)
    {
        return Resource::fetch(urldecode($fileName));
    }

    public function index() {
        $catalogues = Collection::current()->catalogues()->with('resources')->get();

        return View::make('resources.index', compact('catalogues'));
    }

    public function show($catalogueId) {
        $catalogue = Catalogue::with('resources')->firstOrFail();

        return View::make('resources.show', compact('catalogue'));
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

}