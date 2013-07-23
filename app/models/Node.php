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

    public function potentialOwners()
    {
        $user = Sentry::getUser();

        return [
            $user->id => $user->fullName
        ];
    }

    public function nodeTypeTableName()
    {
        return $this->nodetype->tableName();
    }

    public function createDraft($data)
    {
        return DB::table( $this->nodeTypeTableName() )->insertGetId($data);
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

}