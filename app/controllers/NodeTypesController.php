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

    public function update($nodeTypeId)
    {
        $nodeType = NodeType::find($nodeTypeId);

        if (!$nodeType) {
            return Redirect::back()
                ->withErrors(new MessageBag(array('That node type could not be found.' )));
        }

        // Let's run the validator
        $validator = new Core\Validators\NodeType;

        // If the validator fails
        if ($validator->fails()) {
            return \Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $nodeType->label = Input::get('label');
        $nodeType->name = Str::slug($nodeType->label);
        
        // We'll cache this so we can remove any columns
        $removedColumns = $nodeType->columns;
        $nodeType->columns = Input::get('columns');

        $enumCols = array();

        foreach($nodeType->columns as $column) {
            if ($column->category == 'enum' OR $column->category == 'enum-multi') {
                $enumCols[$column->name] = $column->values;
            }
        }

        // Do a check for enum columns...
        if (count($nodeType->columns)) {
            foreach($nodeType->columns as $column) {

                if ($column->category == 'enum' OR $column->category == 'enum-multi') {

                    // Compare the two lists...
                    $removals = array_diff($enumCols[ $column->name ], $column->values);

                    foreach($removals as $removal) {
                        DB::table($nodeType->tableName())
                            ->where( $column->name, '=', $removal)
                            ->update(array( $column->name => DB::raw('NULL')));
                    }

                }

            }
        }

        foreach($removedColumns as $key => $val) {

            foreach($nodeType->columns as $new) {
                if ($new->name == $val->name) {
                    unset($removedColumns[$key]);
                }
            }

        }

        $nodeType->save();

        $nodeType->collections()->sync( Input::get('collections') );

        $nodeType->updateTable($removedColumns);

        return Redirect::route('node-types.index')
                ->with('successes', new MessageBag(array($nodeType->label . ' has been created.')));
    }

    public function formTemplate()
    {
        return NodeType::viewForCategory(Input::get('category'));
    }

    public function destroy($id){
        $nodeType = NodeType::find($id);

        if ( ! $nodeType->delete() ) {
            return Redirect::back()
                ->withErrors(['Sorry, that node ' . $nodeType->label . ' couldn\'t be deleted.']);
        }

        return Redirect::back()
                ->with('successes', new MessageBag([$nodeType->label . ' has been deleted.']) );
    }

}