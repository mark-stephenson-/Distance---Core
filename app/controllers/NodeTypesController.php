<?php

class NodeTypesController extends BaseController
{
    public function index()
    {

        if (!Sentry::getUser()->hasAccess('node-types.index')) {
            die('no-access');
        }

        $nodeTypes = NodeType::all();

        return View::make('node-types.index', compact('nodeTypes'));
    }

    public function create()
    {
        $nodeType = new NodeType;
        $nodeCollections = array();

        return View::make('node-types.form', compact('nodeType', 'nodeCollections'));
    }

    public function store()
    {
        // Let's run the validator
        $validator = new \Core\Validators\NodeType;

        // If the validator fails
        if ($validator->fails()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $nodeType = new NodeType;

        $nodeType->label = Input::get('label');
        $nodeType->columns = Input::get('columns');
        $nodeType->name = Str::slug($nodeType->label);

        $nodeType->save();

        $nodeType->collections()->sync( Input::get('collections') );

        if ( ! $nodeType->createTable() ) {
            return Redirect::route('node-types.index')
                ->with('errors', new MessageBag(array('There was a problem creating the database table for this node type, your data has not been lost.')));
        }

        return Redirect::route('node-types.index')
                ->with('successes', new MessageBag(array($nodeType->label . ' has been created.')));
    }

    public function edit($nodeTypeId)
    {
        $nodeType = NodeType::find($nodeTypeId);

        if (!$nodeType) {
            return Redirect::back()
                ->withErrors(new MessageBag(array('That node type could not be found.')));
        }

        $nodeCollections = array_map(function($value) {
            return $value->id;
        }, $nodeType->collections->all());

        return View::make('node-types.form', compact('nodeType', 'nodeCollections'));
    }

    public function formTemplate()
    {
        return NodeType::viewForCategory(Input::get('category'));
    }

}