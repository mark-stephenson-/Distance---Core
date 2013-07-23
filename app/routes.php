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

// Authentication
Route::get('login', array('as' => 'login', 'uses' => 'AuthController@showLogin'));
Route::get('login/{userId}/{token}', array('uses' => 'AuthController@processReviewerLogin'));
Route::post('login', array('uses' => 'AuthController@processLogin'));

Route::get('forgot-password', array('as' => 'forgot-password', 'uses' => 'AuthController@forgotPassword'));
Route::post('forgot-password', array('uses' => 'AuthController@processForgotPassword'));

Route::get('forgot-password/{code}', array('as' => 'reset-password', 'uses' => 'AuthController@resetPassword'));
Route::post('forgot-password/{code}', array('uses' => 'AuthController@processResetPassword'));

Route::get('logout', array('as' => 'logout', 'uses' => 'AuthController@processLogout'));

Route::filter('auth', 'Core\Filters\Auth@auth');

Route::group(array('before' => 'auth'), function() {

    // Nodes
    Route::get('nodes/hierarchy/{collectionId}', array('as' => 'nodes.hierarchy', 'uses' => 'NodesController@hierarchy'));
    Route::get('nodes/list/{collectionId}', array('as' => 'nodes.list', 'uses' => 'NodesController@nodeList'));

    // CRUD
    Route::any('nodes/view/{nodeId}/{revisionId?}/{branchId?}', array('as' => 'nodes.view', 'uses' => 'NodesController@view'));

    Route::get('nodes/create/{collectionId}/{nodeTypeId?}/{parentId?}', array('as' => 'nodes.create', 'uses' => 'NodesController@create'));
    Route::post('nodes/create/{collectionId}/{nodeTypeId?}/{parentId?}', array('as' => 'nodes.store', 'uses' => 'NodesController@store'));
    
    Route::get('nodes/edit/{nodeId}/{revisionId}/{branchId?}', array('as' => 'nodes.edit', 'uses' => 'NodesController@edit'));
    Route::post('nodes/edit/{nodeId}/{revisionId}/{branchId?}', array('as' => 'nodes.update', 'uses' => 'NodesController@update'));

    Route::resource('collections', 'CollectionsController');

    Route::post('node-types/form-template', array('as' => 'node-types.form-template', 'uses' => 'NodeTypesController@formTemplate'));
    Route::resource('node-types', 'NodeTypesController');

});