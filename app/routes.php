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
        die('access denied');
    }
});

Route::group(array('before' => array('auth')), function() {

    Route::get('me', array('as' => 'me', 'uses' => 'MeController@index'));
    Route::post('me', array('as' => 'me.update', 'uses' => 'MeController@update'));

    Route::group(array('before' => array('auth', 'checkPermissions')), function() {


        Route::resource('apps', 'AppsController');

        Route::group(array('prefix' => 'apps'), function() {
            Route::get('{appId}/collections', array('as' => 'collections.index', 'uses' => 'CollectionsController@index'));
            Route::get('{appId}/collections/create', array('as' => 'collections.create', 'uses' => 'CollectionsController@create'));
            Route::post('{appId}/collections', array('as' => 'collections.store', 'uses' => 'CollectionsController@store'));
            Route::get('{appId}/collections/{id}/edit', array('as' => 'collections.edit', 'uses' => 'CollectionsController@edit'));
            Route::put('{appId}/collections/{id}', array('as' => 'collections.update', 'uses' => 'CollectionsController@update'));

            Route::group(array('prefix' => '{appId}/collections/{collectionId}'), function() {
                /*
                    Nodes View Types
                 */
                Route::get('list', array('as' => 'nodes.list', 'uses' => 'NodesController@nodeList'));
                Route::get('hierarchy', array('as' => 'nodes.hierarchy', 'uses' => 'NodesController@hierarchy'));

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

        // Nodes
        Route::get('collections/{collectionId}/type-list/{nodeTypeName}', array('as' => 'nodes.type-list', 'uses' => 'NodesController@nodeTypeList'));
        Route::any('nodes/update-order/{id?}', 'NodesController@updateOrder');
        Route::get('nodes/node-lookup', array('as' => 'nodes.lookup', 'uses' => 'NodesController@lookup'));
        Route::get('nodes/link/{collectionId?}/{nodeId?}/{parentId?}', array('as' => 'nodes.link', 'uses' => 'NodesController@link'));
        Route::get('nodes/unlink/{collectionId?}/{nodeId?}/{parentId?}', array('as' => 'nodes.unlink', 'uses' => 'NodesController@unlink'));

        // Restful Resource Addons
        Route::get('file/{collectionId}/{filename}', array('as' => 'resources.load', 'uses' => 'ResourcesController@load'));
        Route::post('resources/process/{collectionId}/{catalogId}', array('as' => 'resources.process', 'uses' => 'ResourcesController@process'));

        
        Route::resource('groups', 'GroupsController');
        Route::resource('catalogues', 'CataloguesController');
        Route::get('catalogues/{id}/delete', array('as' => 'catalogues.destroy', 'uses' => 'CataloguesController@destroy'));
        Route::resource('resources', 'ResourcesController');
        Route::get('resources/{id}/delete', array('as' => 'resources.destroy', 'uses' => 'ResourcesController@destroy'));
        Route::post('resources/{id}/update-file', array('as' => 'resources.updateFile', 'uses' => 'ResourcesController@updateFile'));
        Route::resource('app-distribution', 'OtaController');
        Route::post('app-distribution/update', array('as' => 'app-distribution.update', 'uses' => 'OtaController@update'));

        Route::post('node-types/form-template', array('as' => 'node-types.form-template', 'uses' => 'NodeTypesController@formTemplate'));
        Route::resource('node-types', 'NodeTypesController');

        Route::group(array('prefix' => 'ajax'), function() {
            Route::group(array('prefix' => 'resources'), function() {
                Route::get('toggle_sync', 'Ajax\ResourcesController@toggleSync');
            });
        });

        Route::get('courses', array('as' => 'cirrus.courses', 'uses' => 'CoursesController@index'));
    });
});