<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::group(array('before' => array('auth', 'super')), function () {

    Route::get('artisan/{command}', function ($command) {
        $params = Input::all();

        if (count($params) > 0) {
            return Artisan::call($command, $params);
        } else {
            return Artisan::call($command);
        }
    });

});

$appId = Request::segment(2);
$collectionId = Request::segment(4);

if (Request::segment(1) == 'apps' and is_numeric($appId)) {
    define('CORE_APP_ID', $appId);
} else {
    define('CORE_APP_ID', Application::currentId());
}

if (Request::segment(3) == 'collections' and is_numeric($collectionId)) {
    define('CORE_COLLECTION_ID', $collectionId);
} else {
    define('CORE_COLLECTION_ID', Collection::currentId());
}

View::composer('*', function ($view) {
    $view->with('appId', CORE_APP_ID);
    $view->with('collectionId', CORE_COLLECTION_ID);
});

App::error(function (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    Session::forget('current-app');
    Session::forget('current-collection');

    return Redirect::route('root');
});

Route::any('/', array('as' => 'root', function () {
    return Redirect::route('apps.index');
}));

Route::group(array('prefix' => 'api'), function () {
    // We need to ensure that the prfiler doesn't pop up here...
    // Config::set('profiler::config.enabled', false);

    Route::get('/', function () { return Response::make('', 400); });
    Route::put('authentication', array('uses' => 'Api\AuthenticationController@authenticate'));

    Route::group(array('before' => 'apiAuthentication'), function () {
        Route::get('collections', 'Api\CollectionController@collections');
        Route::get('hierarchy', 'Api\HierarchyController@hierarchy');
        Route::get('node/{id}', 'Api\NodeController@node');
        Route::get('node', 'Api\NodeController@nodes');
        Route::post('submit', 'Api\NodeController@add');
        Route::get('emailNode', 'Api\NodeController@emailNode');
        Route::get('node-types', 'Api\NodeTypeController@nodeTypes');
        Route::get('resource/{id}/{language?}', 'Api\ResourceController@resource');
        Route::get('resource', 'Api\ResourceController@resources');
        Route::get('localisations', 'Api\LocalisationController@localisations');
        Route::get('modules', 'Api\ModulesController@modules');
    });
});

// Authentication
Route::get('login', array('as' => 'login', 'uses' => 'AuthController@showLogin'));
Route::get('login/{userId}/{token}', array('uses' => 'AuthController@processReviewerLogin'));
Route::post('login', array('uses' => 'AuthController@processLogin'));

Route::get('forgot-password', array('as' => 'forgot-password', 'uses' => 'AuthController@forgotPassword'));
Route::post('forgot-password', array('uses' => 'AuthController@processForgotPassword'));

Route::get('forgot-password/{user_id}/{code}', array('as' => 'reset-password', 'uses' => 'AuthController@resetPassword'));
Route::post('forgot-password/{user_id}/{code}', array('uses' => 'AuthController@processResetPassword'));

Route::get('logout', array('as' => 'logout', 'uses' => 'AuthController@processLogout'));

Route::post('ota/download', array('uses' => 'OtaController@postDownload'));
Route::post('ota/download/{testing?}', array('uses' => 'OtaController@postDownload'));

Route::get('ota/download', array('as' => 'ota.download.production', 'uses' => 'OtaController@download'));
Route::get('ota/download/{testing?}', array('uses' => 'OtaController@download'));
Route::get('ota/download/testing', array('as' => 'ota.download.testing', 'uses' => 'OtaController@download'));

Route::get('ota/download/deliver/{platform}/{environment}/{version}/{type}', array('as' => 'ota.download.deliver', 'uses' => 'OtaController@deliver'));

Route::filter('auth', 'Core\Filters\Auth@auth');

App::error(function (Symfony\Component\HttpKernel\Exception\HttpException $exception) {
    if ($exception->getStatusCode() == 403) {
        return View::make('403');
    }
});

App::error(function (Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
    return View::make('404');
});

Route::get('file/{catalogueId}/{language}/{filename}', array('as' => 'resources.load', 'uses' => 'ResourcesController@load'));
Route::get('cron', array('as' => 'cron', 'uses' => 'CronController@run'));

Route::group(array('before' => array('auth')), function () {

    /*
        Global - and just checking for login
     */
    Route::get('me', array('as' => 'me', 'uses' => 'MeController@index'));
    Route::post('me', array('as' => 'me.update', 'uses' => 'MeController@update'));

    Route::group(array('before' => array('auth', 'checkPermissions')), function () {

        Route::resource('apps', 'AppsController');

        Route::group(array('prefix' => 'apps'), function () {
            Route::get('{appId}/destroy', array('as' => 'app.destroy', 'uses' => 'AppsController@destroy'));
            Route::get('{appId}/collections', array('as' => 'collections.index', 'uses' => 'CollectionsController@index'));
            Route::get('{appId}/collections/create', array('as' => 'collections.create', 'uses' => 'CollectionsController@create'));
            Route::post('{appId}/collections', array('as' => 'collections.store', 'uses' => 'CollectionsController@store'));
            Route::get('{appId}/collections/{id}/edit', array('as' => 'collections.edit', 'uses' => 'CollectionsController@edit'));
            Route::get('{appId}/collections/{id}/destroy', array('as' => 'collections.destroy', 'uses' => 'CollectionsController@destroy'));
            Route::put('{appId}/collections/{id}', array('as' => 'collections.update', 'uses' => 'CollectionsController@update'));
            Route::get('{appId}/collections/{id}/create-resource-archive', array('as' => 'collections.createResourceArchive', 'uses' => 'CollectionsController@createResourceArchive'));

            /*
                Over The Air Distribution
             */
            Route::get('{appId}/app-distribution', array('as' => 'app-distribution.index', 'uses' => 'OtaController@index'));
            Route::get('{appId}/app-distribution/create', array('as' => 'app-distribution.create', 'uses' => 'OtaController@create'));
            Route::post('{appId}/app-distribution/', array('as' => 'app-distribution.store', 'uses' => 'OtaController@store'));
            Route::post('{appId}/app-distribution/update', array('as' => 'app-distribution.update', 'uses' => 'OtaController@update'));

            Route::group(array('prefix' => '{appId}/collections/{collectionId}'), function () {
                /*
                    Nodes View Types
                 */
                Route::get('list', array('as' => 'nodes.list', 'uses' => 'NodesController@nodeList'));
                Route::get('hierarchy', array('as' => 'nodes.hierarchy', 'uses' => 'NodesController@hierarchy'));
                Route::get('type-list/{nodeTypeName}', array('as' => 'nodes.type-list', 'uses' => 'NodesController@nodeTypeList'));

                /*
                    Nodes CRUD
                 */
                Route::any('nodes/view/{nodeId}/{revisionId?}/{branchId?}', array('as' => 'nodes.view', 'uses' => 'NodesController@view'));
                Route::post('nodes/delete/{nodeId}/{revisionId?}/{branchId?}', array('as' => 'nodes.delete', 'uses' => 'NodesController@doDelete'));

                Route::get('nodes/create/{nodeTypeId?}/{parentId?}', array('as' => 'nodes.create', 'uses' => 'NodesController@create'));
                Route::post('nodes/create/{nodeTypeId?}/{parentId?}', array('as' => 'nodes.store', 'uses' => 'NodesController@store'));

                Route::get('nodes/edit/{nodeId}/{revisionId}/{branchId?}', array('as' => 'nodes.edit', 'uses' => 'NodesController@edit'));
                Route::post('nodes/edit/{nodeId}/{revisionId}/{branchId?}', array('as' => 'nodes.update', 'uses' => 'NodesController@update'));

                /*
                    Node Revisions
                 */
                Route::any('nodes/publish/{nodeId}/{revisionId}/{branchId?}', array('as' => 'nodes.publish', 'uses' => 'NodesController@markAsPublished'));
                Route::any('nodes/retire/{nodeId}/{revisionId}/{branchId?}', array('as' => 'nodes.retire', 'uses' => 'NodesController@markAsRetired'));
                Route::get('create-revision/{nodeId}/{revisionId}/{branchId?}', array(
                    'as' => 'questions.create-revision',
                    'uses' => 'QuestionsController@createRevision'
                ));

                Route::get('publish-revision/{nodeId}/{revisionId}/{branchId?}', array(
                    'as' => 'questions.publish-revision',
                    'uses' => 'QuestionsController@publishRevision'
                ));
                /*
                    Hierarchy Actions
                 */
                Route::any('nodes/update-order/{id?}', 'NodesController@updateOrder');
                Route::get('nodes/node-lookup', array('as' => 'nodes.lookup', 'uses' => 'NodesController@lookup'));
                Route::get('nodes/link/{nodeId?}/{parentId?}', array('as' => 'nodes.link', 'uses' => 'NodesController@link'));
                Route::get('nodes/unlink/{nodeId?}/{parentId?}', array('as' => 'nodes.unlink', 'uses' => 'NodesController@unlink'));

                /*
                    Catalogues & Resources
                 */
                Route::get('catalogues', array('as' => 'catalogues.index', 'uses' => 'CataloguesController@index'));
                Route::get('catalogues/create', array('as' => 'catalogues.create', 'uses' => 'CataloguesController@create'));
                Route::post('catalogues', array('as' => 'catalogues.store', 'uses' => 'CataloguesController@store'));
                Route::get('catalogues/{id}/edit', array('as' => 'catalogues.edit', 'uses' => 'CataloguesController@edit'));
                Route::put('catalogues/{id}', array('as' => 'catalogues.update', 'uses' => 'CataloguesController@update'));
                Route::get('catalogues/{id}/delete', array('as' => 'catalogues.destroy', 'uses' => 'CataloguesController@destroy'));

                Route::get('resources', array('as' => 'resources.index', 'uses' => 'ResourcesController@index'));
                Route::get('resources/{id}/{language}', array('as' => 'resources.show', 'uses' => 'ResourcesController@show'));
                Route::get('resources/{id}/localisations/{rid}/{language}', array('as' => 'resources.localisations', 'uses' => 'ResourcesController@localisations'));
                Route::get('resources/{id}/{language}/delete/{redirect?}', array('as' => 'resources.destroy', 'uses' => 'ResourcesController@destroy'));
                Route::get('resources/{id}/delete/{language?}', array('as' => 'resources.destroyResource', 'uses' => 'ResourcesController@destroyResource'));
                Route::post('resources/{id}/{language}/update-file/{redirect?}', array('as' => 'resources.updateFile', 'uses' => 'ResourcesController@updateFile'));
                Route::post('resources/{id}/{language}/edit-name', array('as' => 'resources.editName', 'uses' => 'ResourcesController@editName'));
                Route::post('resources/process/{catalogueId}/{language?}', array('as' => 'resources.process', 'uses' => 'ResourcesController@process'));
            });
        });

        /*
            Global (with permissions)
         */

        Route::get('nodes/node-lookup/{appId}/{collectionId}', array('as' => 'nodes.lookup', 'uses' => 'NodesController@lookup'));


        Route::group(['prefix' => 'reporting'], function() {
            Route::get('/', [
                'as' => 'reporting.index',
                'uses' => 'ReportingController@index',
            ]);

            Route::get('_ajax/{trustId}/hospitals', [
                'as' => 'reporting.ajax.hospitals',
                'uses' => 'ReportingController@hospitals',
            ]);

            Route::get('_ajax/{hospitalId}/wards', [
                'as' => 'reporting.ajax.wards',
                'uses' => 'ReportingController@wards',
            ]);

            Route::get('_ajax/{wardId}/generate', [
                'as' => 'reporting.ajax.generate',
                'uses' => 'ReportingController@generate',
            ]);

            Route::get('_ajax/update_standard_reports_table', [
                'as' => 'reporting.ajax.generate',
                'uses' => 'ReportingController@updateStandardReportsTable',
            ]);
            
            Route::get('view/{id}', [
                'as' => 'reporting.view',
                'uses' => 'ReportingController@view',
            ]);

            Route::get('view/{id}/csv', [
                'as' => 'reporting.view-csv',
                'uses' => 'ReportingController@viewCsv',
            ]);

            Route::get('view/{id}/pdf', [
                'as' => 'reporting.view-pdf',
                'uses' => 'ReportingController@viewPdf',
            ]);
        });

        Route::get('data/export/download', ['as' => 'data.export.download', function () {
            return Response::download(storage_path('export-csvs/export.zip'));
        }]);
        Route::get('data/export/{filename?}', ['as' => 'data.export', function ($filename = null) {

            $questionSets = Node::where('node_type', 7)
                ->orderBy('status')
                ->orderBy('published_at')
                ->get();

            return View::make('data-export.choose', compact('questionSets'));
        }]);

        Route::post('data/export', ['as' => 'data.export.work', function () {
            if (PRRecord::first() == null) {
                return View::make('data-export.empty');
            }

            try {
                $exportService = \App::make('Core\Services\ExportService');
                $exportPath = $exportService->generateExportForQuestionSet(Input::get('question_set'));
            } catch (\Exception $e) {
                return Redirect::route('data.export')->withErrors(new Illuminate\Support\MessageBag([$e->getMessage()]));
            }

            return View::make('data-export.download', compact('exportPath'));
        }]);

        /*
            Users
         */
        Route::resource('users', 'UsersController');
        Route::get('users/{id}/add-group/{group_id}', array('as' => 'users.add-group', 'uses' => 'UsersController@doAddGroup'));
        Route::get('users/{id}/remove-group/{group_id}', array('as' => 'users.remove-group', 'uses' => 'UsersController@doRemoveGroup'));
        Route::get('users/delete/{id}', array('as' => 'users.delete', 'uses' => 'UsersController@delete'));
        Route::get('users/{id}/groups', array('as' => 'users.manageGroups', 'uses' => 'UsersController@getGroups'));

        /*
            Volunteers
        */
        Route::group(array('prefix' => 'volunteers'), function () {
            Route::get('/', array(
                'as' => 'volunteers.index',
                'uses' => 'VolunteersController@index',
            ));

            Route::get('create', array(
                'as' => 'volunteers.create',
                'uses' => 'VolunteersController@create',
            ));

            Route::post('create', array(
                'as' => 'volunteers.store',
                'uses' => 'VolunteersController@store',
            ));

            Route::get('edit/{id}', array(
                'as' => 'volunteers.edit',
                'uses' => 'VolunteersController@edit',
            ));

            Route::post('edit/{id}', array(
                'as' => 'volunteers.update',
                'uses' => 'VolunteersController@update',
            ));

            Route::get('delete/{id}', array(
                'as' => 'volunteers.delete',
                'uses' => 'VolunteersController@delete',
            ));
        });

        /*
         * Manage Questionnaires
         */
        Route::group(array('prefix' => 'questionnaires'), function () {
            Route::get('/', array(
                'as' => 'questionnaires.index',
                'uses' => 'QuestionsController@index',
            ));

            Route::get('view/{node_id}/{revision_id?}/{branch_id?}', array(
                'as' => 'questionnaires.view',
                'uses' => 'QuestionsController@view',
            ));

            Route::get('create/{node_id}/{parent_id?}', array(
                'as' => 'questionnaires.create',
                'uses' => 'QuestionsController@create',
            ));

            Route::get('edit/{node_id}/{revision_id?}/{branch_id?}', array(
                'as' => 'questionnaires.edit',
                'uses' => 'QuestionsController@edit',
            ));

            Route::post('store/{nodetypeId}/{parentId}', array(
                'as' => 'questionnaires.store',
                'uses' => 'QuestionsController@store',
            ));


            Route::post('update/{node_id}/{revision_id?}/{branch_id?}', array(
                'as' => 'questionnaires.update',
                'uses' => 'QuestionsController@update',
            ));

            Route::get('create-revision/{nodeId}/{revisionId}/{branchId?}', array(
                'as' => 'questionnaires.create-revision',
                'uses' => 'QuestionsController@createRevision'
            ));

            Route::get('publish-revision/{nodeId}/{revisionId}/{branchId?}', array(
                'as' => 'questionnaires.publish-revision',
                'uses' => 'QuestionsController@publishRevision'
            ));

            Route::get('delete/{node_id}/{revision_id?}/{branch_id?}', array(
                'as' => 'questionnaires.delete',
                'uses' => 'QuestionsController@delete',
            ));
        });

        /*
            Manage Trusts, Hospitals & Wards
        */
        Route::group(array('prefix' => 'manage-trust'), function () {
            Route::get('/', array(
                'as' => 'manage.index',
                'uses' => 'ManageController@index',
            ));

            Route::group(array('prefix' => 'trust'), function () {
                Route::get('{id}', array(
                    'as' => 'manage.trust.index',
                    'uses' => 'ManageController@trust',
                ))->where('id', '[0-9]+');

                Route::get('create', array(
                    'as' => 'manage.trust.create',
                    'uses' => 'ManageController@createTrust',
                ));

                Route::post('create', array(
                    'as' => 'manage.trust.store',
                    'uses' => 'ManageController@storeTrust',
                ));

                Route::get('{id}/edit', array(
                    'as' => 'manage.trust.edit',
                    'uses' => 'ManageController@editTrust',
                ));

                Route::post('{id}/edit', array(
                    'uses' => 'ManageController@updateTrust',
                ));

                Route::get('{id}/delete', array(
                    'as' => 'manage.trust.delete',
                    'uses' => 'ManageController@deleteTrust',
                ));

                Route::group(array('prefix' => '{trustId}/hospital'), function () {
                    Route::get('{hospitalId}', array(
                        'as' => 'manage.hospital.index',
                        'uses' => 'ManageController@hospital',
                    ))->where('hospitalId', '[0-9]+');

                    Route::get('create', array(
                        'as' => 'manage.hospital.create',
                        'uses' => 'ManageController@createHospital',
                    ));

                    Route::post('create', array(
                        'as' => 'manage.hospital.store',
                        'uses' => 'ManageController@storeHospital',
                    ));

                    Route::get('{hospitalId}/edit', array(
                        'as' => 'manage.hospital.edit',
                        'uses' => 'ManageController@editHospital',
                    ));

                    Route::post('{hospitalId}/edit', array(
                        'uses' => 'ManageController@updateHospital',
                    ));

                    Route::get('{hospitalId}/delete', array(
                        'as' => 'manage.hospital.delete',
                        'uses' => 'ManageController@deleteHospital',
                    ));

                    Route::group(array('prefix' => '{hospitalId}/ward'), function () {
                        Route::get('create', array(
                            'as' => 'manage.ward.create',
                            'uses' => 'ManageController@createWard',
                        ));

                        Route::post('create', array(
                            'as' => 'manage.ward.store',
                            'uses' => 'ManageController@storeWard',
                        ));

                        Route::get('{wardId}/delete', array(
                            'as' => 'manage.ward.delete',
                            'uses' => 'ManageController@deleteWard',
                        ));

                        Route::post('{id}/delete', array(
                            'uses' => 'ManageController@performDeleteWard',
                        ));

                        Route::get('{id}/edit', array(
                            'as' => 'manage.ward.edit',
                            'uses' => 'ManageController@editWard',
                        ));

                        Route::post('{id}/edit', array(
                            'uses' => 'ManageController@updateWard',
                        ));

                        Route::get('{id}/merge', array(
                            'as' => 'manage.ward.merge',
                            'uses' => 'ManageController@mergeWard',
                        ));

                        Route::post('{id}/merge', array(
                            'uses' => 'ManageController@performMergeWard',
                        ));
                    });
                });
            });
        });

        /*
            Groups
         */
        Route::resource('groups', 'GroupsController');
        Route::get('groups/delete/{id}', array('as' => 'groups.delete', 'uses' => 'GroupsController@delete'));

        /*
            Node Types
         */
        Route::post('node-types/form-template', array('as' => 'node-types.form-template', 'uses' => 'NodeTypesController@formTemplate'));
        Route::resource('node-types', 'NodeTypesController');
        Route::get('node-types/{id}/destroy', array('as' => 'node-types.destroy', 'uses' => 'NodeTypesController@destroy'));

        Route::group(array('prefix' => 'ajax'), function () {
            Route::group(array('prefix' => 'resources'), function () {
                Route::get('toggle_sync', 'Ajax\ResourcesController@toggleSync');
                Route::get('toggle_pub', 'Ajax\ResourcesController@togglePub');
            });
        });
    });
});
