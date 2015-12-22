<?php

class QuestionsController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function createRevision($appId, $collectionId, $nodeId, $revisionId, $branchId)
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

    public function publishRevision($appId, $collectionId, $nodeId, $revisionId, $branchId)
    {
        // First retire the current set
        $existingPublishedSet = Node::where('status', 'published')->where('node_type', 7)->first();

        if ($existingPublishedSet) {
            $existingHierarchyItem = Hierarchy::where('node_id', $existingPublishedSet->id)->first();

            if ($existingHierarchyItem) {
                $existingHierarchyItem->findChildren(1);

                foreach ($existingHierarchyItem->getChildren() as $oldQuestionLeaf) {
                    $oldRevision = $oldQuestionLeaf->node->latestRevision();
                    $oldQuestionLeaf->node->markAsRetired($oldRevision->id);

                    $oldQuestionLeaf->delete();
                }

                $oldRevision = $existingHierarchyItem->node->latestRevision();
                $existingHierarchyItem->node->markAsRetired($oldRevision->id);

                $existingHierarchyItem->delete();
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
