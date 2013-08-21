<?php

class AppsController extends BaseController
{

    public function show($appId) {
        return Redirect::route('collections.index', array($appId));
    }
    
    public function index() {
        $apps = Application::with('collections')->get();

        return View::make('apps.index', compact('apps'));
    }

    public function create() {
        $app = new Application;
        $collections = Collection::get();

        return View::make('apps.form', compact('app', 'collections'));
    }

    public function store() {
        // Let's run the validator
        $validator = new Core\Validators\App;

        // If the validator fails
        if ($validator->fails()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $app = new Application;

        $app->name = Input::get('name');
        $app->api_key = Input::get('api_key');

        $app->save();

        $app->collections()->sync(Input::get('collections', array()));

        return Redirect::route('apps.index')
                ->with('successes', new MessageBag(array($app->name . ' has been created.')));
    }

    public function edit($appId) {
        $app = Application::with('collections')->findOrFail($appId);
        $collections = Collection::get();

        return View::make('apps.form', compact('app', 'collections'));
    }

    public function update($appId) {

        $app = Application::findOrFail($appId);

        // Let's run the validator
        $validator = new Core\Validators\App;

        // If the validator fails
        if ($validator->fails()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $app->name = Input::get('name');
        $app->api_key = Input::get('api_key');
        $app->collections()->sync(Input::get('collections', array()));

        $app->save();


        return Redirect::route('apps.index')
                ->with('successes', new MessageBag(array($app->name . ' has been updated.')));
    }

    public function delete() {

    }

}