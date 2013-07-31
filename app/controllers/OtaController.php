<?php

class OtaController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('ota.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$new = new Ota;
		return View::make('ota.form', compact('new'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = new Core\Validators\Ota;

		if ( $validator->fails() ) {
			return Redirect::back()
				->withInput()
				->withErrors($validator->messages());
		}

		$file = Input::file('file');

		if ( $file->getError() ) {
			return Redirect::back()
			            ->withInput()
			            ->withErrors(array('Filesize is too big'));
		}

		// Validator has passed.
		$path = base_path() . '/resources/ota/' . Input::get('platform') . '/' . Input::get('environment') . '/' . Input::get('version');
		@mkdir($path, 0777, true);

		// Move stuff into place
		if ( Input::get('platform') == "ios") {
			Input::file('file')->move($path, 'app.ipa');

			// \App::make('ipa-distribution', array(base_path() . '/resources/ota/ios/' . Input::get('environment') . '/' . Input::get('version') . '/app.ipa', Input::get('environment'), Input::get('version')));
		} else if ( Input::get('platform') == "android") {
			Input::file('file')->move($path, 'app.apk');
		}

		// Insert into the database.
		$version = new Ota;
		$version->platform = Input::get('platform');
		$version->environment = Input::get('environment');
		$version->version = Input::get('version');
		$version->release_notes = Input::get('release_notes');

		if ( ! $version->save() ) {
			return Redirect::back()
				->withErrors(new MessageBag(array('Version ' . $version->version . ' for ' . $version->platform . ' could not been created.')));
		}

		return Redirect::route('app-distribution.index')
			->with('successes', new MessageBag(array($version->version . ' for ' . $version->platform . ' has been created.')));
	}

	public function update()
	{
		$validator = new Core\Validators\OtaPasswords;

		if ( $validator->fails() ) {
			return Redirect::back()
				->withInput()
				->withErrors($validator->messages());
		}

		Setting::updateConfig('ota-production_password', Input::get('production_password'));
		Setting::updateConfig('ota-testing_password', Input::get('testing_password'));

		return Redirect::back()
			->with('successes', new MessageBag(array('The distribution passwords have been updated successfully.')));
	}
}