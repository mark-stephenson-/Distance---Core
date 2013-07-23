<?php

class NodesController extends BaseController
{

    public function hierarchy($collectionId = 0) {

        if (!Sentry::getUser()->hasAccess('')) {
            die('no-access');
        }

        $collection = Collection::find($collectionId);

        if (!$collection) {
            return Redirect::back()
                ->withErrors(['That collection could not be found.']);
        }

        Session::put('current-collection', $collection);
        Session::put('collection-node-view', 'hierarchy');

        $branches = $collection->hierarchy;
        $branches->findChildren();

        return View::make('nodes.hierarchy', compact('collection', 'branches'));
    }

    public function nodeList($collectionId = 0) {

        if (!Sentry::getUser()->hasAccess('')) {
            die('no-access');
        }

        $collection = Collection::find($collectionId);

        if (!$collection) {
            return Redirect::back()
                ->withErrors(['That collection could not be found.']);
        }

        Session::put('current-collection', $collection);
        Session::put('collection-node-view', 'list');

        $nodes = $collection->nodes;

        return View::make('nodes.list', compact('collection', 'nodes'));

    }

    public function view($node_id, $revision_id = false, $branch_id = false)
    {
        $node = Node::find($node_id);

        if (!$node) {
            return Redirect::back()
                ->withErrors(new MessageBag(array('That node could not be found.' )));
        }
        
        if ($revision_id == 'branch') {
            $revision_id = null;
        }

        $revisionData = $node->fetchRevision($revision_id);
        // $breadcrumbs = $node->generateBreadcrumbsFromBranch($branch_id);

        $branch = ($branch_id) ? Hierarchy::find($branch_id) : new Hierarchy;
        $revisions = $node->revisions();
        $nodeType = $node->nodetype;
        $collection = $node->collection;
        
        return View::make('nodes.view', compact(
            'branch','nodeType','node','revisionData', 'revisionAuthor', 'revisions','collection'
        ));
    }

    public function create($collectionId, $nodeTypeId, $parentId = false) {
        if (!Sentry::getUser()->hasAccess('')) {
            die('no-access');
        }

        $nodeType = NodeType::find($nodeTypeId);
        $node = new Node;
        $node->owned_by = Sentry::getUser()->id;
        $node->created_by = Sentry::getUser()->id;
        $node->collection_id = $collectionId;

        $collection = Collection::find($collectionId);

        return View::make('nodes.form', compact('collection', 'nodeType', 'node', 'parentId'));
    }

    public function store($collectionId, $nodeTypeId, $parentId = false) {
        if (!Sentry::getUser()->hasAccess('')) {
            die('no-access');
        }


        $nodeType = NodeType::find($nodeTypeId);
        $node = new Node;
        $node->title = Input::get('title');
        $node->owned_by = Input::get('owned_by');
        $node->created_by = Sentry::getUser()->id;
        $node->node_type = $nodeTypeId;
        $node->collection_id = $collectionId;

        // Grab the submitted content and check if any fields are required
        $nodetypeContent = Input::get('nodetype');
        $nodeColumnErrors = $node->nodetype->checkRequiredColumns($nodetypeContent);

        // Let's run the validator
        $validator = new Core\Validators\Node;

        // If the validator or the required column check fails
        if ($validator->fails() or count($nodeColumnErrors)) {

            // We have missing columns! Add to the bag and send the user back
            foreach($nodeColumnErrors as $err) {
                $validator->messages()->add($err, "The $err field is required.");
            }

            return Redirect::refresh()
                ->withInput()
                ->withErrors($validator->messages());
        }

        if (!$node->save()) {

            $validator->messages()->add('unknown', 'An unknown error occured');

            return Redirect::refresh()
                ->withInput()
                ->withErrors($validator->messages());
        }

        // Let's create the first revision
        $nodetypeContent = $nodeType->parseColumns($nodetypeContent);
        $nodetypeContent['node_id'] = $node->id;
        $nodetypeContent['status'] = "draft";
        $nodetypeContent['created_by'] = $nodetypeContent['updated_by'] = Sentry::getUser()->id;
        $nodetypeContent['created_at'] = $nodetypeContent['updated_at'] = DB::raw('NOW()');

        $nodeDraft = $node->createDraft($nodetypeContent);

        if (!$nodeDraft) {

            $validator->messages()->add('unknown', 'An unknown error occured');

            return Redirect::refresh()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $node->latest_revision = $nodeDraft;
        $node->status = 'draft';
        $node->save();

        if (is_numeric($parentId)) {

            $branch = new Hierarchy;
            $branch->node_id = $node->id;
            $branch->collection_id = $node->collection_id;
            $branch->created_by = Sentry::getUser()->id;

            if ($parentId == 0) {
                // We need to find the collection root
                $parent = Hierarchy::where('collection_id', '=', $node->collection_id)->first();
            } else {
                $parent = Hierarchy::find($parentId);
            }

            // Assuming the parent exists...
            $branch->makeFirstChildOf($parent);

            return Redirect::route('nodes.hierarchy', array($node->collection_id))
                ->with('successes', new MessageBag(array("The node {$node->title} has been created.")));
        } else {
            return Redirect::route('nodes.list', array($node->collection_id))
                ->with('successes', new MessageBag(array("The node {$node->title} has been created.")));
        }

    }

}