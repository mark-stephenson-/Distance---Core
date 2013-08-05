<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('login');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

Route::filter('apiAuthentication', function()
{
    if ( Request::header('User-Token') ) {
        $user = User::whereKey( Request::header('User-Token') )->first();

        if ( ! $user ) {
            return Response::make('User Token Invalid.', 403);
        }

        App::singleton('user', function() use ($user) {
            return Sentry::getUserProvider()->findById($user->id);
        });
    }

    if ( Request::header('Collection-Token') ) {
        $collection = Collection::whereApiKey( Request::header('Collection-Token'))->first();

        if ( ! $collection ) {
            return Response::make('Collection Token Invalid.', 403);
        }

        App::singleton('collection', function() use ($collection) {
            return $collection;
        });
    }

    if ( Request::header('Authorization-Token') or ( Request::header('Authorization-Token') === NULL) ) {
        $authorization = Application::whereApiKey( Request::header('Authorization-Token'))->first();

        if ( ! $authorization ) {
            return Response::make('Authorization Token Invalid.', 403);
        }

        App::singleton('app', function() use ($authorization) {
            return $authorization;
        });
    }

});

Route::filter('checkPermissions', function($request)
{
    $replacements = [
        'index' => '*',
        'view' => '*',
        'controller' => '',
        '\\' => '.',
        '@' => '.',

        'edit' => 'update',
        'store' => 'create',
    ];

    $property = 'cms.' . str_replace(array_keys($replacements), array_values($replacements), strtolower($request->getAction()));

    if (starts_with($property, 'cms.nodes')) {
        return;
    }

    Log::debug($property);

    if ( ! Sentry::getUser()->hasAccess( $property ) ) {
        App::abort(403);
    }
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});