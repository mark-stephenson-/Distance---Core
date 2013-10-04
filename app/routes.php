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

View::composer('*', function($view)
{
    $view->with('appId', CORE_APP_ID);
    $view->with('collectionId', CORE_COLLECTION_ID);
});

Route::any('/', array('as' => 'root', function() {
    return Redirect::route('apps.index');
}));

Route::group( array('prefix' => 'api'), function() {
    // We need to ensure that the prfiler doesn't pop up here...
    // Config::set('profiler::config.enabled', false);
    
    Route::get('/', function(){ return Response::make('', 400); });
    Route::put('authentication', array('uses' => 'Api\AuthenticationController@authenticate'));

    Route::group( array('before' => 'apiAuthentication'), function() {
        Route::get('collections', 'Api\CollectionController@collections');
        Route::get('hierarchy', 'Api\HierarchyController@hierarchy');
        Route::get('node/{id}', 'Api\NodeController@node');
        Route::get('node', 'Api\NodeController@nodes');
        Route::get('emailNode', 'Api\NodeController@emailNode');
        Route::get('node-types', 'Api\NodeTypeController@nodeTypes');
        Route::get('resource/{id}', 'Api\ResourceController@resource');
        Route::get('resource', 'Api\ResourceController@resources');
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

Route::filter('auth', 'Core\Filters\Auth@auth');

App::error(function(Symfony\Component\HttpKernel\Exception\HttpException $exception)
{
    if ($exception->getStatusCode() == 403) {
        return View::make('403');
    }
});

App::error(function(Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
    return View::make('404');
});

Route::get('file/{collectionId}/{filename}', array('as' => 'resources.load', 'uses' => 'ResourcesController@load'));

Route::group(array('before' => array('auth')), function() {

    /*
        Global - and just checking for login
     */
    Route::get('me', array('as' => 'me', 'uses' => 'MeController@index'));
    Route::post('me', array('as' => 'me.update', 'uses' => 'MeController@update'));


    Route::group(array('before' => array('auth', 'checkPermissions')), function() {


        Route::resource('apps', 'AppsController');

        Route::group(array('prefix' => 'apps'), function() {
            Route::get('{appId}/destroy', array('as' => 'app.destroy', 'uses' => 'AppsController@destroy'));
            Route::get('{appId}/collections', array('as' => 'collections.index', 'uses' => 'CollectionsController@index'));
            Route::get('{appId}/collections/create', array('as' => 'collections.create', 'uses' => 'CollectionsController@create'));
            Route::post('{appId}/collections', array('as' => 'collections.store', 'uses' => 'CollectionsController@store'));
            Route::get('{appId}/collections/{id}/edit', array('as' => 'collections.edit', 'uses' => 'CollectionsController@edit'));
            Route::put('{appId}/collections/{id}', array('as' => 'collections.update', 'uses' => 'CollectionsController@update'));

            /*
                Over The Air Distribution
             */
            Route::get('{appId}/app-distribution', array('as' => 'app-distribution.index', 'uses' => 'OtaController@index'));
            Route::get('{appId}/app-distribution/create', array('as' => 'app-distribution.create', 'uses' => 'OtaController@create'));
            Route::post('{appId}/app-distribution/', array('as' => 'app-distribution.store', 'uses' => 'OtaController@store'));
            Route::post('{appId}/app-distribution/update', array('as' => 'app-distribution.update', 'uses' => 'OtaController@update'));

            Route::group(array('prefix' => '{appId}/collections/{collectionId}'), function() {
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

                Route::get('nodes/create/{nodeTypeId?}/{parentId?}', array('as' => 'nodes.create', 'uses' => 'NodesController@create'));
                Route::post('nodes/create/{nodeTypeId?}/{parentId?}', array('as' => 'nodes.store', 'uses' => 'NodesController@store'));
                
                Route::get('nodes/edit/{nodeId}/{revisionId}/{branchId?}', array('as' => 'nodes.edit', 'uses' => 'NodesController@edit'));
                Route::post('nodes/edit/{nodeId}/{revisionId}/{branchId?}', array('as' => 'nodes.update', 'uses' => 'NodesController@update'));

                /*
                    Node Revisions
                 */
                Route::any('nodes/publish/{nodeId}/{revisionId}/{branchId?}', array('as' => 'nodes.publish', 'uses' => 'NodesController@markAsPublished'));
                Route::any('nodes/retire/{nodeId}/{revisionId}/{branchId?}', array('as' => 'nodes.retire', 'uses' => 'NodesController@markAsRetired'));

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
                Route::get('resources/{id}', array('as' => 'resources.show', 'uses' => 'ResourcesController@show'));
                Route::get('resources/{id}/delete', array('as' => 'resources.destroy', 'uses' => 'ResourcesController@destroy'));
                Route::post('resources/{id}/update-file', array('as' => 'resources.updateFile', 'uses' => 'ResourcesController@updateFile'));
                Route::post('resources/process/{catalogId}', array('as' => 'resources.process', 'uses' => 'ResourcesController@process'));

            });
        });

        /*
            Global (with permissions)
         */
        
        /*
            Users
         */
        Route::resource('users', 'UsersController');
        Route::get('users/{id}/add-group/{group_id}', array('as' => 'users.add-group', 'uses' => 'UsersController@doAddGroup'));
        Route::get('users/{id}/remove-group/{group_id}', array('as' => 'users.remove-group', 'uses' => 'UsersController@doRemoveGroup'));

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
        Route::get('node-types/{id}/destroy', ['as' => 'node-types.destroy', 'uses' => 'NodeTypesController@destroy']);

        Route::group(array('prefix' => 'ajax'), function() {
            Route::group(array('prefix' => 'resources'), function() {
                Route::get('toggle_sync', 'Ajax\ResourcesController@toggleSync');
                Route::get('toggle_pub', 'Ajax\ResourcesController@togglePub');
            });
        });

        Route::get('courses', array('as' => 'cirrus.courses', 'uses' => 'CoursesController@index'));
    });
});