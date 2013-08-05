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
    return Redirect::route('collections.index');
}));

Route::group( ['prefix' => 'api'], function() {
    Route::put('authentication', array('uses' => 'Api\AuthenticationController@authenticate'));

    Route::group( ['before' => 'apiAuthentication' ], function() {
        Route::get('nodes', function() { return ''; });
        Route::get('collections', 'Api\CollectionController@collections');
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

Route::group(array('before' => ['auth', 'checkPermissions']), function() {

    Route::get('me', array('as' => 'me', 'uses' => 'MeController@index'));
    Route::post('me', array('as' => 'me.update', 'uses' => 'MeController@update'));

    // Nodes
    Route::get('collections/{collectionId}/hierarchy', array('as' => 'nodes.hierarchy', 'uses' => 'NodesController@hierarchy'));
    Route::get('collections/{collectionId}/list', array('as' => 'nodes.list', 'uses' => 'NodesController@nodeList'));
    Route::get('collections/{collectionId}/type-list/{nodeTypeName}', array('as' => 'nodes.type-list', 'uses' => 'NodesController@nodeTypeList'));
    Route::any('nodes/update-order/{id?}', 'NodesController@updateOrder');
    Route::get('nodes/node-lookup', array('as' => 'nodes.lookup', 'uses' => 'NodesController@lookup'));
    Route::get('nodes/link/{collectionId?}/{nodeId?}/{parentId?}', array('as' => 'nodes.link', 'uses' => 'NodesController@link'));
    Route::get('nodes/unlink/{collectionId?}/{nodeId?}/{parentId?}', array('as' => 'nodes.unlink', 'uses' => 'NodesController@unlink'));

    Route::any('collections/{collectionId}/nodes/publish/{nodeId}/{revisionId}/{branchId?}', array('as' => 'nodes.publish', 'uses' => 'NodesController@markAsPublished'));
    Route::any('collections/{collectionId}/nodes/retire/{nodeId}/{revisionId}/{branchId?}', array('as' => 'nodes.retire', 'uses' => 'NodesController@markAsRetired'));

    // CRUD
    Route::any('collections/{collectionId}/nodes/view/{nodeId}/{revisionId?}/{branchId?}', array('as' => 'nodes.view', 'uses' => 'NodesController@view'));

    Route::get('collections/{collectionId}/nodes/create/{nodeTypeId?}/{parentId?}', array('as' => 'nodes.create', 'uses' => 'NodesController@create'));
    Route::post('collections/{collectionId}/nodes/create/{nodeTypeId?}/{parentId?}', array('as' => 'nodes.store', 'uses' => 'NodesController@store'));
    
    Route::get('collections/{collectionId}/nodes/edit/{nodeId}/{revisionId}/{branchId?}', array('as' => 'nodes.edit', 'uses' => 'NodesController@edit'));
    Route::post('collections/{collectionId}/nodes/edit/{nodeId}/{revisionId}/{branchId?}', array('as' => 'nodes.update', 'uses' => 'NodesController@update'));

    // Restful Resource Addons
    Route::get('file/{filename}', array('as' => 'resources.load', 'uses' => 'ResourcesController@load'));
    Route::post('resources/process/{catalogId}', array('as' => 'resources.process', 'uses' => 'ResourcesController@process'));

    Route::resource('collections', 'CollectionsController');
    Route::resource('users', 'UsersController');
    Route::resource('groups', 'GroupsController');
    Route::resource('apps', 'AppsController');
    Route::resource('catalogues', 'CataloguesController');
    Route::resource('resources', 'ResourcesController');
    Route::resource('app-distribution', 'OtaController');
    Route::post('app-distribution/update', array('as' => 'app-distribution.update', 'uses' => 'OtaController@update'));

    Route::post('node-types/form-template', array('as' => 'node-types.form-template', 'uses' => 'NodeTypesController@formTemplate'));
    Route::resource('node-types', 'NodeTypesController');

});