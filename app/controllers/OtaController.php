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
	public function store($appId)
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

		return Redirect::route('app-distribution.index', array($appId))
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

	public function download($environment = 'production')
    {
        $version = Ota::current();

        if (strpos($_SERVER['HTTP_USER_AGENT'], 'IEMobile') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'Windows Phone')) {
            $version = $version->windows();
        } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false) {
            $version = $version->android();
        } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false or strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false) {
            $version = $version->iOS();
        } else {
            return \View::make('downloads.desktop');
        }

        $version = $version->whereEnvironment($environment)->first();

        if (!$version) {
            return \View::make('downloads.404', ['message' => "A $environment version of this app does not exist."]);
        }

        $password = Setting::getConfig("ota-{$environment}_password");

        if (\Input::get('password')) {

            if (md5($password) == \Input::get('password')) {
                // We're good!\
                return \View::make('downloads.index')->with('version', $version);
            } else {
                return \View::make('downloads.password')->with('version', $version)->with('error', 'Invalid password entered.');
            }
        } else {
            return \View::make('downloads.password')->with('version', $version);
        }
    }

    public function postDownload($environment = 'production')
    {
        $url = route('ota.download.' . $environment) . '?password=' . md5(\Input::get('password'));
        return \Redirect::to($url);
    }

    public function deliver($platform, $environment, $version, $type)
    {
        // Find the version
        $version = Ota::wherePlatform($platform)->whereEnvironment($environment)->whereVersion($version)->first();

        if (!$version) {
            return \App::abort(404);
        }

        $filePath = $version->filePath($type);

        // Work out the mime type
        switch($type) {
            case 'app':
                $mime = 'application/octet-stream';
                break;
            case 'profile':
            case 'certificate':
                $mime = 'application/c-x509-ca-cert';
                break;
            case 'manifest':
                $mime = 'text/xml';
                break;
            default:
                return \App::abort(500);
                break;
        }

        $responseHeaders = array(
            'Content-Type' => $mime,
            'Content-Transfer-Encoding' => 'binary',
            'Content-Description'       => 'File Transfer',
        );

        if ($platform == 'android') {
            $responseHeaders['Content-Disposition'] = 'attachment; filename="ignaz.apk"';
            $responseHeaders['Content-Type'] = 'application/vnd.android.package-archive';
        }

        if ($platform == 'windows') {
            if ($type == 'manifest') {
                $responseHeaders['Content-Disposition'] = 'attachment; filename="ignaz.xap"';
                $responseHeaders['Content-Type'] = 'application/x-silverlight-app';
            } else {
                $responseHeaders['Content-Disposition'] = 'attachment; filename="ignaz.aetx"';
            }
        }

        return \Response::make(
            \File::get($filePath), 200, $responseHeaders
        );
    }
}
