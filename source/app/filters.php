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

App::before(function ($request) {
    if (App::environment('production')) {
        Request::setTrustedProxies([
            '172.27.30.240',
        ]);
    }
});

App::after(function ($request, $response) {
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

Route::filter('auth', function () {
    if (!Sentry::check()) {
        return Redirect::guest('login');
    }
});

Route::filter('auth.basic', function () {
    return Auth::basic();
});

Route::filter('apiAuthentication', function () {
    \Carbon\Carbon::setToStringFormat("Y-m-d\TH:i:s\Z");

    if (Request::header('User-Token')) {
        $user = User::whereKey(Request::header('User-Token'))->first();

        if (!$user) {
            return Response::make('User Token Invalid.', 403);
        }

        App::singleton('user', function () use ($user) {
            return Sentry::getUserProvider()->findById($user->id);
        });
    }

    if (Request::header('Collection-Token')) {
        $collection = Collection::whereApiKey(Request::header('Collection-Token'))->first();

        if (!$collection) {
            return Response::make('Collection Token Invalid.', 403);
        }

        App::singleton('collection', function () use ($collection) {
            return $collection;
        });
    }

    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="Authorization"');
        header('HTTP/1.0 401 Unauthorized');

        return Response::make('Authorization Token Invalid.', 403);
        exit;
    } else {
        $authorization = Application::whereApiKey($_SERVER['PHP_AUTH_USER'])->first();

        if (!$authorization) {
            return Response::make('Authorization Token Invalid.', 403);
        }

        App::singleton('app', function () use ($authorization) {
            return $authorization;
        });
    }

});

Route::filter('checkPermissions', function ($request) {
    $additionalProperties = array();
    $params = $request->parameters();

    $replacements = array(
        '.list' => '',
        '.hierarchy' => '',
        '.type-list' => '',
        'edit' => 'update',
        'resources' => 'catalogues',
    );

    $property = 'cms.'.implode('.', Request::segments());

    $property = str_replace(array_keys($replacements), array_values($replacements), $property);

    // Editing a user gives the user ID, let's detect and avoid that
    if (
        (starts_with($property, 'cms.users.') and ends_with($property, '.groups')) or
        (starts_with($property, 'cms.users.') and str_contains($property, '.add-group')) or
        (starts_with($property, 'cms.users.') and str_contains($property, '.remove-group'))
    ) {
        $property = 'cms.users.addgroup';
    }

    if (
        (starts_with($property, 'cms.users.') and ends_with($property, '.update')) or
        (starts_with($property, 'cms.users.') and is_numeric(substr($property, -1, 1)))
    ) {
        $property = 'cms.users.update';
    }


    // Same with volunteers
    if (starts_with($property, 'cms.volunteers.')) {
        $property = 'cms.volunteers.manage';
    }

    if (starts_with($property, 'cms.questionnaires')) {
        $property = 'cms.questionnaires.manage';
    }

    if ((starts_with($property, 'cms.reporting'))) {
        $property = 'cms.export-data.manage';
    }

    if ((starts_with($property, 'cms.manage-trust'))) {
        $property = 'cms.manage-trust.manage';
    }

    if ((starts_with($property, 'cms.apps.1.collections.1.questions.create-revision'))) {
        $property = 'cms.apps.1.collections.1.question.revision-management';
    }

    if (
        (str_contains($property, '.catalogues.') and ends_with($property, '.update')) or
        (str_contains($property, '.catalogues.') and is_numeric(substr($property, -1, 1)))
    ) {
        $nodesPos = strpos($property, '.catalogues.');
        $property = substr($property, 0, $nodesPos).'.catalogues.update';
    }

    if ($nodesPos = strpos($property, '.nodes')) {
        $property = substr($property, 0, $nodesPos);
    }

    if ($collectionsPos = strpos($property, '.collections')) {
        $additionalProperties[] = substr($property, 0, $collectionsPos).'.collection-management';
    }

    $properties = array_merge($additionalProperties, array($property, $property.'.*'));

    if (!Sentry::getUser()->hasAnyAccess($properties)) {
        if (Request::segment(1) == 'apps' and count(Request::segments()) == 1) {
            return Redirect::to('/me');
        }

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

Route::filter('guest', function () {
    if (Auth::check()) {
        return Redirect::to('/');
    }
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

Route::filter('csrf', function () {
    if (Session::token() != Input::get('_token')) {
        throw new Illuminate\Session\TokenMismatchException();
    }
});
