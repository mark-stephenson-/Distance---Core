<?php

namespace Core\Services;

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
}
