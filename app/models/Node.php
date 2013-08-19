<?php

class Node extends BaseModel
{

    public function collection()
    {
        return $this->belongsTo('Collection');
    }

    public function nodetype()
    {
        return $this->belongsTo('NodeType', 'node_type');
    }

    public function getNodeTypeLabelAttribute()
    {
        return $this->nodetype->name;
    }

    public function getStatusBadgeAttribute()
    {
        switch($this->getAttribute('status')) {
            case 'published':
                $badge = 'success';
                break;
            case 'retired':
                $badge = 'inverse';
                break;
            default:
                $badge = '';
                break;
        }

        return '<span class="label label-' . $badge . '">' . ucfirst($this->getAttribute('status')) . '</span>';
    }

    public function potentialOwners()
    {
        $user = Sentry::getUser();

        return array(
            $user->id => $user->fullName
        );
    }

    public function nodeTypeTableName()
    {
        return $this->nodetype->tableName();
    }

    public function createDraft($data)
    {
        return DB::table( $this->nodeTypeTableName() )->insertGetId($data);
    }

    public function updateDraft($data, $revisionId)
    {
        return DB::table( $this->nodeTypeTableName() )->whereId($revisionId)->update($data);
    }

    public function markAsPublished($revisionId)
    {
        // There will only a maximum of one published node, set it as retired
        // we don't check the success of this, as it could return 0 which would
        // end up as false
        DB::table( $this->nodeTypeTableName() )
            ->where('status', '=', 'published')
            ->where('node_id', '=', $this->getAttribute('id'))
            ->update(array('status' => 'retired'));

        // And mark the new one as published
        $publishUpdate = DB::table( $this->nodeTypeTableName() )
                            ->where('id', '=', $revisionId)
                            ->where('node_id', '=', $this->getAttribute('id'))
                            ->update(array('status' => 'published'));

        // We also need to update the main node table
        $this->status = 'published';
        $this->published_revision = $revisionId;
        $this->published_at = DB::raw('NOW()');
        $this->retired_at = DB::raw('NULL');

        $nodeUpdate = $this->save();

        return ($publishUpdate and $nodeUpdate);
    }

    public function markAsRetired($revisionId)
    {
        $retireUpdate = DB::table($this->nodeTypeTableName())
                            ->where('status', '=', 'published')
                            ->where('node_id', '=', $this->getAttribute('id'))
                            ->where('id', '=', $revisionId)
                            ->update(array('status' => 'retired'));

        $this->retired_at = DB::raw('NOW()');
        $this->status = 'retired';

        $nodeUpdate = $this->save();

        return ($retireUpdate and $nodeUpdate);
    }

    public function revisions($amount = 10)
    {
        return $this->fetchRevision(null, $amount);
    }

    public function fetchRevision($revision_id = null, $amount = 1)
    {
        $revision = DB::table( $this->nodeTypeTableName() )
                        ->orderBy('updated_at', 'desc')
                        ->where('node_id', '=', $this->getAttribute('id'))
                        ->select(array($this->nodeTypeTableName() . '.*'));

        if (is_numeric($revision_id)) {
            $revision->where($this->nodeTypeTableName() . '.id', '=', $revision_id);
        }

        if (is_int($amount)) {
            $revision->take($amount);

            if ($amount == 1) {
                $return = $revision->first();
            } else {
                $return = $revision->get();
            }
        } else {
            $return = $revision->get();
        }

        if (is_array($return)) {
            foreach($return as &$ret) {
                $ret->user = Sentry::getUserProvider()->findById($ret->updated_by);
            }
        } else {
            $return->user = Sentry::getUserProvider()->findById($return->updated_by);
        }

        return $return;
    }

    public function scopeIsPublished($query)
    {
        $query->whereStatus('published');
    }

}