<?php

namespace Core\Services;

use Hierarchy;
use Illuminate\Http\Request;
use Node;
use NodeType;
use Sentry;
use DB;

class NodeService
{
    public function createPublishedNodeOfTypeWithData($type, $title, $data = [])
    {
        $node = new Node();
        $node->title = $title;
        $node->created_by = $node->owned_by = Sentry::getUser()->id;
        $node->node_type = $type;
        $node->collection_id = CORE_COLLECTION_ID;
        $node->status = 'published';
        $node->published_at = DB::raw('NOW()');
        $node->save();

        $nodeType = NodeType::find($type);

        // All good
        $nodeRevision = $nodeType->parseColumns($data, [], false);
        $nodeRevision['updated_at'] = DB::raw('NOW()');
        $nodeRevision['status'] = 'published';
        $nodeRevision['created_by'] = $node->created_by;
        $nodeRevision['updated_by'] = $node->created_by;
        $nodeRevision['node_id'] = $node->id;

        $nodeResult = $node->createDraft($nodeRevision);

        $node->latest_revision = $nodeResult;
        $node->published_revision = $nodeResult;
        $node->save();

        return $node;
    }

    public function updatePublishedNodeWithData(Node $node, $data = [], $title = null)
    {
        if ($title) {
            $node->title = $title;
            $node->save();
        }

        $latestRevision = $node->latestRevision();

        $node->updateDraft($data, $latestRevision->id);

        return $node;
    }

    public function checkForUniquenessInType($type, $key, $value, $currentId = null)
    {
        $uniqueCheck = \DB::table("node_type_{$type}")
            ->where($key, $value);

        if ($currentId) {
            $uniqueCheck->where('node_id', '!=', $currentId);
        }

        if ($uniqueCheck->count() > 0) {
            return "The {$key} field must be unique.";
        }
    }

    public function updateNode($formData, Node $node, $revisionId, $branchId)
    {
        if ($branchId) {
            $branch = Hierarchy::find($branchId);
        }

        // Fetch the existing data
        $revisionData = $node->fetchRevision($revisionId);

        // We can update the title no problem...
        $node->title = $formData['title'];
        $node->owned_by = $formData['owned_by'];

        // Grab the submitted content and check if any fields are required
        $translations = isset($formData['translation']) ? $formData['translation'] : array();
        $isRevision = $revisionData->status == 'published';
        $nodeTypeContent = isset($formData['nodetype']) ? $formData['nodetype'] : array();

        // That's the main title updated... now for the node content...
        $type = $node->nodetype;
        $nodeRevision = $type->parseColumns($nodeTypeContent, $translations, $isRevision);

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

            $node->touch();
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

        return (boolean) $nodeResult;
    }


    public function createNode($formData, $nodeTypeId, $parentId)
    {
        $nodeType = NodeType::find($nodeTypeId);
        $node = new Node;
        $node->title = $formData['title'];
        $node->owned_by = $formData['owned_by'];
        $node->created_by = Sentry::getUser()->id;
        $node->node_type = $nodeTypeId;
        $node->collection_id = CORE_COLLECTION_ID;
        $node->save();

        // Grab the submitted content and check if any fields are required
        $nodetypeContent = isset($formData['nodetype']) ? $formData['nodetype'] : array();
        $translations = isset($formData['translation']) ? $formData['translation'] : array();

        // Let's create the first revision
        $nodetypeContent = $nodeType->parseColumns($nodetypeContent, $translations, false);
        $nodetypeContent['node_id'] = $node->id;
        $nodetypeContent['status'] = "draft";
        $nodetypeContent['created_by'] = $nodetypeContent['updated_by'] = Sentry::getUser()->id;
        $nodetypeContent['created_at'] = $nodetypeContent['updated_at'] = DB::raw('NOW()');

        $nodeDraft = $node->createDraft($nodetypeContent);

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
                $parent = Hierarchy::whereNodeId($parentId)->first();
            }
            // Assuming the parent exists...
            $branch->makeFirstChildOf($parent);

        }

        return $node;
    }
    
}
