<?php

class NodesController extends BaseController
{

    public function hierarchy($appId, $collectionId = 0) {

        $collection = Collection::find($collectionId);

        if (!$collection) {
            return Redirect::back()
                ->withErrors(array('That collection could not be found.'));
        }

        Session::put('current-collection', $collection->id);
        Session::put('collection-node-view', 'hierarchy');

        $branches = $collection->hierarchy;
        $branches->findChildren();

        $nodeTypes = NodeType::arrayOfTypes();

        return View::make('nodes.hierarchy', compact('collection', 'branches', 'nodeTypes'));
    }

    public function nodeList($appId, $collectionId = 0) {
        if ( Input::get('filter') or Input::get('sort') ) {
            $collection = Collection::with(array('nodes' => function($query) {
                if ( Input::get('filter') ) {
                    $query->where('node_type', '=', Input::get('filter'));
                }

                if ( Input::get('sort') ) {
                    $query->orderBy('title', Input::get('sort'));
                }
            }))->find($collectionId);
        } else {
            $collection = Collection::find($collectionId);
        }

        if (!$collection) {
            return Redirect::back()
                ->withErrors(array('That collection could not be found.'));
        }

        Session::put('last-view', array('url' => Request::fullUrl(), 'collection_id' => $collectionId));
        Session::put('current-collection', $collection->id);
        Session::put('collection-node-view', 'list');

        $nodes = $collection->nodes;

        return View::make('nodes.list', compact('collection', 'nodes'));

    }

    public function nodeTypeList($appId, $collectionId = 0, $nodeTypeName = '')
    {
        $collection = Collection::find($collectionId);
        $nodeType = NodeType::where('name', '=', $nodeTypeName)->firstOrFail();

        if (!$collection) {
            return Redirect::back()
                ->withErrors(array('That collection could not be found.'));
        }

        Session::put('current-collection', $collection->id);
        Session::put('collection-node-view', 'list');

        $nodes = $collection->nodes()->where('node_type', '=', $nodeType->id)->get();

        return View::make('nodes.list', compact('collection', 'nodes', 'nodeType'));
    }

    public function view($appId, $collectionId, $node_id, $revision_id = false, $branch_id = false)
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

        $branch = ($branch_id) ? Hierarchy::find($branch_id) : new Hierarchy;
        $revisions = $node->revisions();
        $nodeType = $node->nodetype;
        $collection = $node->collection;

        if ($branch->exists) {
            $breadcrumbPath = $branch->getPath();

            // We need to shave off the first item as that's the collection root
            array_shift($breadcrumbPath);

            // ... and the last as we can hard-code that
            array_pop($breadcrumbPath);

            $breadcrumbs = Hierarchy::with('node');

            if ( count($breadcrumbPath) ) {
                $breadcrumbs = $breadcrumbs->whereIn('id', $breadcrumbPath)->get();
            }
        } else {
            $breadcrumbs = array();
        }
        
        return View::make('nodes.view', compact(
            'branch','nodeType','node','revisionData', 'revisionAuthor', 'revisions', 'collection', 'breadcrumbs'
        ));
    }

    public function create($appId, $collectionId, $nodeTypeId, $parentId = false) {
        
        $nodeType = NodeType::find($nodeTypeId);
        $node = new Node;
        $node->owned_by = Sentry::getUser()->id;
        $node->created_by = Sentry::getUser()->id;
        $node->collection_id = $collectionId;

        $collection = Collection::find($collectionId);

        return View::make('nodes.form', compact('collection', 'nodeType', 'node', 'parentId'));
    }

    public function store($appId, $collectionId, $nodeTypeId, $parentId = false) {
        
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

            return Redirect::route('nodes.view', array($appId, $node->collection_id, $node->id))
                ->with('successes', new MessageBag(array("The node {$node->title} has been created.")));
        } else {
            return Redirect::route('nodes.view', array($appId, $node->collection_id, $node->id))
                ->with('successes', new MessageBag(array("The node {$node->title} has been created.")));
        }

    }

    public function edit($appId, $collectionId, $nodeId, $revisionId, $branchId = false)
    {
        $node = Node::find($nodeId);

        if (!$node) {
            return Redirect::back()
                ->withErrors(new MessageBag(array('That node could not be found.' )));
        }

        if ($branchId) {
            $branch = Hierarchy::find($branchId);
        }

        $revisionData = $node->fetchRevision($revisionId);
        // $breadcrumbs = $node->generateBreadcrumbsFromBranch($branchId);
        $collection = $node->collection;
        $nodeType = $node->nodetype;
        $revisions = $node->revisions();

        return View::make('nodes.form', compact(
            'nodeType','node','revisionData','revisions', 'collection', 'branchId'
        ));
    }

    public function update($appId, $collectionId, $nodeId, $revisionId, $branchId = false)
    {
        $node = Node::find($nodeId);
        $bag = new \MessageBag();

        if (!$node) {
            return Redirect::back()
                ->withErrors(new MessageBag(array('That node could not be found.' )));
        }

        if ($branchId) {
            $branch = Hierarchy::find($branchId);
        }

        // Fetch the existing data
        $revisionData = $node->fetchRevision($revisionId);

        // We can update the title no problem...
        $node->title = Input::get('title');
        $node->owned_by = Input::get('owned_by');

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

        if (! $node->save() ) {

            $validator->messages()->add('unknown', 'An unknown error occured');

            return Redirect::refresh()
                ->withInput()
                ->withErrors($validator->messages());
        }

        // That's the main title updated... now for the node content...
        $type = $node->nodetype;
        $nodeRevision = $type->parseColumns($nodetypeContent);

        // If a user doesn't have permission for a certain column... the POST key will not exist, we'll
        // re-populate that from the old revision (if possible)
        if ($node->latest_revision) {
            $oldRevision = $node->fetchRevision($node->latest_revision);

            if ($oldRevision) {
                // Loop through and find the missing items
                foreach($type->columns as $type_column) {
                    if (!isset($nodeRevision[$type_column->name])) {
                        $nodeRevision[$type_column->name] = $oldRevision->{$type_column->name};
                    }
                }
            }
        }

        // Is the revision we are editing something that can be updated?
        if ($revisionData->status == 'draft') {
            $nodeAction = 'update';

            $nodeRevision['updated_at'] = DB::raw('NOW()');
            $nodeRevision['status'] = 'draft';

            $nodeResult = $node->updateDraft($nodeRevision, $revisionId);
        } else {
            // Can't be updated! We'll make a new draft
            $nodeAction = 'create';

            $nodeRevision['created_by'] = $nodeRevision['updated_by'] = Sentry::getUser()->id;
            $nodeRevision['node_id'] = $node->id;
            $nodeRevision['status'] = 'draft';
            $nodeRevision['created_at'] = $nodeRevision['updated_at'] = \DB::raw('NOW()');

            $nodeResult = $node->createDraft($nodeRevision);

            $node->latest_revision = $nodeResult;
            $node->save();
        }

        // Problem?
        if (!$nodeResult) {
            return Redirect::refresh()
                ->withInput()
                ->withErrors(new MessageBag(array('There was a problem updating your data, if this continues please contact the adminstrator.')));
        }

        if ($nodeAction == 'create') {
            return Redirect::route('nodes.view', array($appId, $collectionId, $node->id, $nodeResult, $branchId))
                ->with('successes', new MessageBag(array('The node ' . $node->title . ' has been updated.')));
        } else {
            return Redirect::route('nodes.view', array($appId, $collectionId, $node->id, $revisionData->id, $branchId))
                ->with('successes', new MessageBag(array('The node ' . $node->title . ' has been updated.')));
        }
    }

    public function link($appId, $collectionId, $nodeId, $parentId = 0)
    {
        // We can just add the link and redirect back...
        $branch = new Hierarchy;
        $branch->collection_id = $collectionId;
        $branch->node_id = $nodeId;
        $branch->created_by = Sentry::getUser()->id;

        if ($parentId == 0) {
            // We need to find the collection root
            $parent = Hierarchy::where('collection_id', '=', $collectionId)->first();
        } else {
            $parent = Hierarchy::find($parentId);
        }

        // Assuming the parent exists...
        $branch->makeFirstChildOf($parent);
        return \Redirect::back()
            ->with('successes', new \MessageBag(array('The node has been linked.')));
    }

    public function unlink($appId, $collectionId, $branchId)
    {
        $branch = Hierarchy::find($branchId);

        if ($branch) {
            $branch->deleteWithChildren();
            return Redirect::back()
                    ->with('successes', new MessageBag(array('The node link has been deleted.')));
        } else {
            return Redirect::back()
                    ->with('errors', new MessageBag(array('The node link could not deleted.')));
        }
    }

    public function markAsPublished($appId, $collectionId, $nodeId, $revisionId, $branchId = false)
    {
        $node = Node::find($nodeId);

        if (!$node) {
            return Redirect::back()
                ->withErrors(new MessageBag(array('That node could not be found.' )));
        }

        if ($node->markAsPublished($revisionId)) {
            return Redirect::route('nodes.view', array($appId, $collectionId, $nodeId, $revisionId, $branchId))
                ->with('successes', new MessageBag(array('The revision has been published.')));
        } else {
            return Redirect::route('nodes.view', array($appId, $collectionId, $nodeId, $revisionId, $branchId))
                ->withErrors(new MessageBag(array('There was an error publishing the revision.')));
        }

    }

    public function markAsRetired($appId, $collectionId, $nodeId, $revisionId, $branchId = false)
    {
        $node = Node::find($nodeId);

        if (!$node) {
            return Redirect::back()
                ->withErrors(new MessageBag(array('That node could not be found.' )));
        }

        if ($node->markAsRetired($revisionId)) {
            return Redirect::route('nodes.view', array($appId, $collectionId, $nodeId, $revisionId, $branchId))
                ->with('successes', new MessageBag(array('The revision has been retired.')));
        } else {
            return Redirect::route('nodes.view', array($appId, $collectionId, $nodeId, $revisionId, $branchId))
                ->withErrors(new MessageBag(array('There was an error retiring the revision.')));
        }

    }

    public function lookup($appId, $collectionId)
    {
        $search = Input::get('q');

        $nodes = Node::
                    where('title', 'LIKE', '%' . $search . '%')
                    ->where('collection_id', '=', $collectionId)
                    ->take(20);

        if (Input::get('type')) {
            $nodes->where('node_type', '=', Input::get('type'));
        }

        $nodes = $nodes->get(array('id', 'title'));

        $output = array();
        foreach ($nodes as $node) {
            $output[] = array('id' => $node->id, 'text' => $node->title);
        }

        return json_encode(array('results' => $output));
    }

    public function updateOrder($appId, $collectionId) {
        $order = json_decode(urldecode(Input::get('order')));

        // We now have a list of all the nodes currently added to this collection ($collection->nodes) 
        // and the order from jQuery nestable ($order)
        $collection = Collection::find($collectionId);
        $saveOrder = Hierarchy::updateOrder($order, $collection);
    }

}