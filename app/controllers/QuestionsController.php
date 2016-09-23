<?php

use Core\Services\NodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class QuestionsController extends BaseController
{
    protected $nodeService;

    public function __construct(NodeService $nodeService)
    {
        parent::__construct();

        $this->nodeService = $nodeService;
    }

    public function index()
    {
        $branches = Hierarchy::first();

        $branches->findChildren();

        $nodeTypes = NodeType::get();
        return View::make('questionnaires.index', compact('branches', 'nodeTypes'));
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

        return View::make('questionnaires.view', compact(
            'branch','nodeType','node','revisionData', 'revisionAuthor', 'revisions', 'collection', 'breadcrumbs'
        ));
    }

    public function create($nodeTypeId, $parentId = false)
    {
        $nodeType = NodeType::find($nodeTypeId);
        $node = new Node;
        $node->owned_by = Sentry::getUser()->id;
        $node->created_by = Sentry::getUser()->id;

        return View::make('questionnaires.form', compact('nodeType', 'node', 'parentId'));
    }

    public function store($nodeTypeId, $parentId)
    {
        $formData = Input::all();

        $nodeType = NodeType::find($nodeTypeId);

        $nodeColumnErrors = $nodeType->checkRequiredColumns($formData['nodetype']);

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

        $node = $this->nodeService->createNode($formData, $nodeTypeId, $parentId);

        if($node) {
            return Redirect::route('questionnaires.view', array($node->id))
                ->with('successes', new MessageBag(array("The node {$formData['title']} has been saved.")));
        }
    }

    public function edit($nodeId, $revisionId = false, $branchId = false)
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
        $nodeType = $node->nodetype;
        $revisions = $node->revisions();

        return View::make('questionnaires.form', compact(
            'nodeType', 'node', 'revisionData', 'revisions', 'branchId'
        ));
    }

    public function update($nodeId, $revisionId = false, $branchId = false)
    {
        $node = Node::find($nodeId);

        if (!$node) {
            return Redirect::back()
                ->withErrors(new MessageBag(array('That node could not be found.' )));
        }

        $formData = Input::all();

        $nodeColumnErrors = $node->nodetype->checkRequiredColumns($formData['nodetype']);

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

        $nodeUpdated = $this->nodeService->updateNode($formData, $node, $revisionId, $branchId);

        if($nodeUpdated) {
            return Redirect::route('questionnaires.view', array($node->id))
                ->with('successes', new MessageBag(array("The node {$formData['title']} has been saved.")));
        }
    }

    public function createRevision($nodeId, $revisionId, $branchId)
    {
        $oldQuestionSet = Node::find($nodeId);
        $oldBranch = Hierarchy::find($branchId);
        $oldBranch->findChildren(1);

        $newQuestionSet = new Node();
        $newQuestionSet->status = 'draft';

        foreach (array('title', 'created_by', 'owned_by', 'collection_id', 'node_type') as $propertyToCopy) {
            $newQuestionSet->{$propertyToCopy} = $oldQuestionSet->{$propertyToCopy};
        }

        $newQuestionSet->save();

        // Question Set nodetypes are empty
        $nodeRevision = array();
        $nodeRevision['updated_at'] = DB::raw('NOW()');
        $nodeRevision['status'] = 'draft';
        $nodeRevision['created_by'] = $newQuestionSet->created_by;
        $nodeRevision['updated_by'] = $newQuestionSet->created_by;
        $nodeRevision['node_id'] = $newQuestionSet->id;
        $nodeResult = $newQuestionSet->createDraft($nodeRevision);

        $newQuestionSet->latest_revision = $nodeResult;
        $newQuestionSet->save();

        // The new question set needs to go in the root of the hierarchy
        $newBranch = new Hierarchy();
        $newBranch->node_id = $newQuestionSet->id;
        $newBranch->collection_id = $newQuestionSet->collection_id;
        $newBranch->created_by = Sentry::getUser()->id;
        $parent = Hierarchy::where('collection_id', '=', $newQuestionSet->collection_id)->first();

        // Assuming the parent exists...
        $newBranch->makeFirstChildOf($parent);

        // OK! So we have our question set, let's loop through the children and duplicate them
        foreach (array_reverse($oldBranch->getChildren()) as $oldQuestion) {
            $newQuestion = new Node();
            $newQuestion->status = 'draft';

            foreach (array('title', 'created_by', 'owned_by', 'collection_id', 'node_type') as $propertyToCopy) {
                $newQuestion->{$propertyToCopy} = $oldQuestion->node->{$propertyToCopy};
            }

            $newQuestion->save();
            $newQuestionRevision = array();
            $newQuestionRevision['updated_at'] = DB::raw('NOW()');
            $newQuestionRevision['status'] = 'draft';
            $newQuestionRevision['created_by'] = $newQuestion->created_by;
            $newQuestionRevision['updated_by'] = $newQuestion->created_by;
            $newQuestionRevision['node_id'] = $newQuestion->id;

            $oldRevision = $oldQuestion->node->latestRevision();

            foreach (array('answertypes', 'domain', 'reversescore') as $propertyToCopy) {
                $newQuestionRevision[$propertyToCopy] = $oldRevision->{$propertyToCopy};
            }

            // The next nightmare is the localization
            $langKey = $oldRevision->question;
            $newLangKey = I18nString::nextKey();
            $langStrings = I18nString::whereKey($langKey)->get();

            foreach ($langStrings as $string) {
                $newString = new I18nString();
                $newString->key = $newLangKey;
                $newString->lang = $string->lang;
                $newString->value = $string->value;
                $newString->save();
            }

            $newQuestionRevision['question'] = $newLangKey;

            $nodeResult = $newQuestion->createDraft($newQuestionRevision);

            $newQuestion->latest_revision = $nodeResult;
            $newQuestion->save();

            // The new question set needs to go in the root of the hierarchy
            $newLeaf = new Hierarchy();
            $newLeaf->node_id = $newQuestion->id;
            $newLeaf->collection_id = $newQuestion->collection_id;
            $newLeaf->created_by = Sentry::getUser()->id;

            // Assuming the parent exists...
            $newLeaf->makeFirstChildOf($newBranch);
        }

        return Redirect::back()->with('successes', new MessageBag(array('A new revision of the question set has been created.')));
    }

    public function publishRevision($nodeId, $revisionId, $branchId)
    {
        // First retire the current set
        $existingPublishedSet = Node::where('status', 'published')->where('node_type', $this->questionSetNodeType)->first();

        if ($existingPublishedSet) {
            $existingHierarchyItem = Hierarchy::where('node_id', $existingPublishedSet->id)->first();

            if ($existingHierarchyItem) {
                $existingHierarchyItem->findChildren(1);

                foreach ($existingHierarchyItem->getChildren() as $oldQuestionLeaf) {
                    $oldRevision = $oldQuestionLeaf->node->latestRevision();
                    $oldQuestionLeaf->node->markAsRetired($oldRevision->id);
                }

                $oldRevision = $existingHierarchyItem->node->latestRevision();
                $existingHierarchyItem->node->markAsRetired($oldRevision->id);
            }
        }

        // Now publish the new set
        $newPublishedSet = Node::find($nodeId);
        $newHierarchyItem = Hierarchy::find($branchId);

        $newHierarchyItem->findChildren(1);

        foreach ($newHierarchyItem->getChildren() as $newQuestionLeaf) {
            $oldRevision = $newQuestionLeaf->node->latestRevision();
            $newQuestionLeaf->node->markAsPublished($oldRevision->id);
        }

        $oldRevision = $newHierarchyItem->node->latestRevision();
        $newHierarchyItem->node->markAsPublished($oldRevision->id);

        return Redirect::back()->with('successes', new MessageBag(array('The questions set has been published and the old set retired')));
    }
}
