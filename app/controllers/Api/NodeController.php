<?php

namespace Api;

use Api;
use Node;
use Resource;
use User;
use Response;
use Request;
use Input;
use PRRecord;
use PRNote;
use PRQuestion;
use PRConcern;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class NodeController extends \BaseController
{
    public function nodes($forAPI = true)
    {
        if (Request::header('Collection-Token') === null) {
            return Response::make('Collection-Token must be specified for this call.', 400);
        }

        $collection = \App::make('collection');
        $nodes = Node::whereCollectionId($collection->id)->isPublished();

        if (Input::get('nodeType')) {
            $nodeType = \NodeType::find(Input::get('nodeType'));

            if (!$nodeType) {
                return Response::make(null, 404);
            }

            $nodes = $nodes->whereNodeType(Input::get('nodeType'));
        } elseif (Input::get('name')) {
            $nodeType = \NodeType::where('name', Input::get('name'))->first();

            if (!$nodeType) {
                return Response::make(null, 404);
            }

            $nodes = $nodes->whereNodeType($nodeType->id);
        }

        if (Input::get('modifiedSince')) {
            $carbon = new \Carbon\Carbon(Input::get('modifiedSince'));
            $nodes = $nodes->where('updated_at', '>', $carbon->toDateTimeString());
        }

        $nodes = $nodes->get();

        if (Input::get('modifiedSince') and (count($nodes) == 0)) {
            return Response::make('', 304);
        }

        if (Input::get('headersOnly') == null or Input::get('headersOnly') == 'false') {
            foreach ($nodes as &$node) {
                if ($node->published_revision) {
                    $node = $this->doExtended($node);
                }
            }

            // Need to go through and sort the node types (to make this a bit easier later on)
            $return = array();

            foreach ($nodes->toArray() as $node) {
                $return[str_plural($node['nodetype']['name'])][] = $node;
            }
        } else {
            $return = $nodes->toArray();
        }

        if ($forAPI === true) {
            return Api::makeResponse($return, 'nodes');
        } else {
            return $return;
        }
    }

    public function newWards()
    {
        $errors = null;

        if (Request::header('Collection-Token') === null) {
            return Response::make('Collection-Token must be specified for this call.', 400);
        }

        $collection = \App::make('collection');

        $request = Request::instance();
        $content = $request->getContent();

        if (!$content) {
            return Response::make('No content recieved', 400);
        }

        $nodeType = \NodeType::where('name', 'ward')->get()->first();

        $requestUser = User::where('email', '=', 'core.admin@thedistance.co.uk')->first();
        $data = json_decode($content, true);

        foreach ($data['wards'] as $ward) {
            $hospital = \Node::find($data['hospital']);
            $node = new Node();
            $middle = '';

            if (strpos($ward, 'Ward') > 0) {
                $ex = explode(' ', $ward)[0];
                if (strpos($ward, ':') > -1) {
                    $middle = explode(':', $ward)[0];
                    $middle = explode(' ', $middle);
                    if (count($middle) > 1) {
                        $_middle = '';
                        foreach ($middle as $word) {
                            $_middle .= ucfirst($word[0]);
                        }
                        $middle = $_middle;
                    } else {
                        $middle = $middle[0];
                    }
                } else {
                    $_middle = '';
                    foreach (explode(' ', $ward) as $word) {
                        $middle .= ucfirst($word[0]);
                    }
                }
            } elseif (strpos($ward, 'Ward') === 0) {
                $num = explode(' ', $ward)[1];
                $middle = explode(':', $num)[0];
            } else {
                $middle = '';
                foreach (explode(' ', $ward) as $word) {
                    $middle .= ucfirst($word[0]);
                }
            }
            $hosname = $hospital->title;

            $node->title = 'ward_'.$middle.'_'.$hosname;
            $node->owned_by = $requestUser->id;
            $node->created_by = $requestUser->id;
            $node->node_type = $nodeType->id;
            $node->collection_id = $collection->id;

            if (!$node->save()) {
                return Response::make('Node for submission could not be created.', 400);
            }

            $name = $ward;
            $hospital = $hospital->id;

            $nodetypeContent = compact('name', 'hospital');
            $nodeColumnErrors = $node->nodetype->checkRequiredColumns($nodetypeContent);

            $nodetypeContent = $nodeType->parseColumns($nodetypeContent, null, false);
            $nodetypeContent['node_id'] = $node->id;
            $nodetypeContent['status'] = 'draft';
            $nodetypeContent['created_by'] = $nodetypeContent['updated_by'] = $requestUser->id;
            $nodetypeContent['created_at'] = $nodetypeContent['updated_at'] = \DB::raw('NOW()');

            $nodeDraft = $node->createDraft($nodetypeContent);

            if (!$nodeDraft) {
                return Response::make('Draft node for submission could not be created.', 400);
            }

            $node->latest_revision = $nodeDraft;
            $node->status = 'draft';
            if (!$node->save()) {
                $errors[] = json_encode($ward).' failed';
            } else {
                $node->markAsPublished($node->fetchRevision()->id);
            }
        }

        if ($node->save()) {
            return Response::make(array('success' => true, 'error' => $errors), 201);
        }

        return Response::make(array('success' => false, 'error' => 'Node for submission could not be saved.'), 500);
    }

    // MARK: temporary script to add new users
    public function newUsers()
    {
        $errors = null;

        if (Request::header('Collection-Token') === null) {
            return Response::make('Collection-Token must be specified for this call.', 400);
        }

        $collection = \App::make('collection');

        $request = Request::instance();
        $content = $request->getContent();

        if (!$content) {
            return Response::make('No content recieved', 400);
        }

        $nodeType = \NodeType::where('name', 'user')->get()->first();

        $requestUser = User::where('email', '=', 'core.admin@thedistance.co.uk')->first();
        $data = json_decode($content, true);

        foreach ($data as $user) {
            $node = new Node();
            $node->title = $user[0].' '.$user[1];
            $node->owned_by = $requestUser->id;
            $node->created_by = $requestUser->id;
            $node->node_type = $nodeType->id;
            $node->collection_id = $collection->id;

            if (!$node->save()) {
                return Response::make('Node for submission could not be created.', 400);
            }

            $first = strtolower($user[0]);
            $last = strtolower($user[1]);

            $firstName = $user[0];
            $lastName = $user[1];
            $username = $first.$last;
            $password = 'prase'.$last;
            $ward = null;

            $nodetypeContent = compact('username', 'password', 'firstName', 'lastName', 'ward');
            $nodeColumnErrors = $node->nodetype->checkRequiredColumns($nodetypeContent);

            $nodetypeContent = $nodeType->parseColumns($nodetypeContent, null, false);
            $nodetypeContent['node_id'] = $node->id;
            $nodetypeContent['status'] = 'draft';
            $nodetypeContent['created_by'] = $nodetypeContent['updated_by'] = $requestUser->id;
            $nodetypeContent['created_at'] = $nodetypeContent['updated_at'] = \DB::raw('NOW()');

            $nodeDraft = $node->createDraft($nodetypeContent);

            if (!$nodeDraft) {
                return Response::make('Draft node for submission could not be created.', 400);
            }

            $node->latest_revision = $nodeDraft;
            $node->status = 'draft';
            if (!$node->save()) {
                $errors[] = json_encode($user).' failed';
            } else {
                $node->markAsPublished($node->fetchRevision()->id);
            }
        }

        if ($node->save()) {
            return Response::make(array('success' => true, 'error' => $errors), 201);
        }

        return Response::make(array('success' => false, 'error' => 'Node for submission could not be saved.'), 500);
    }

    public function add()
    {
        if (Request::header('Collection-Token') === null) {
            return Response::make('Collection-Token must be specified for this call.', 400);
        }

        // MARK: temporary check for header value to add new users
        if (Request::header('Add-Type') == 'User') {
            return $this->newUsers();
        } elseif (Request::header('Add-Type') == 'Ward') {
            return $this->newWards();
        }

        $collection = \App::make('collection');

        $request = Request::instance();
        $content = $request->getContent();

        if (!$content) {
            return Response::make('No content recieved', 400);
        }

        $nodeType = \NodeType::where('name', 'submission')->get()->first();

        if (!$nodeType) {
            // Node type doesn't exist yet, create it
            $nodeType = new \NodeType();
            $nodeType->name = 'submission';
            $nodeType->label = 'Submission';
            $nodeType->columns = array('5424335d11505' => array(
                'category' => 'code',
                'label' => 'json',
                'syntax' => 'json',
                'description' => 'The JSON of a submission',
            ));

            if (!$nodeType->save()) {
                return Response::make('Node type for a submission could not be created.', 400);
            }

            $nodeType->collections()->sync(array($collection->id));

            if (!$nodeType->createTable()) {
                return Response::make('There was a problem creating the database table for this node type, your data has not been lost.', 400);
            }
        }

        $user = User::where('email', '=', 'hello+prase@thedistance.co.uk')->first();

        $node = new Node();
        $node->title = 'Submission'.(Node::where('node_type', $nodeType->id)->count() + 1);
        $node->owned_by = $user->id;
        $node->created_by = $user->id;
        $node->node_type = $nodeType->id;
        $node->collection_id = $collection->id;

        if (!$node->save()) {
            return Response::make('Node for submission could not be created.', 400);
        }

        $nodetypeContent = array('json' => Request::instance()->getContent());
        $nodeColumnErrors = $node->nodetype->checkRequiredColumns($nodetypeContent);

        $nodetypeContent = $nodeType->parseColumns($nodetypeContent, null, false);
        $nodetypeContent['node_id'] = $node->id;
        $nodetypeContent['status'] = 'draft';
        $nodetypeContent['created_by'] = $nodetypeContent['updated_by'] = $user->id;
        $nodetypeContent['created_at'] = $nodetypeContent['updated_at'] = \DB::raw('NOW()');

        $nodeDraft = $node->createDraft($nodetypeContent);

        if (!$nodeDraft) {
            return Response::make('Draft node for submission could not be created.', 400);
        }

        $node->latest_revision = $nodeDraft;
        $node->status = 'draft';

        if ($node->save()) {
            try {
                $data = json_decode(Request::instance()->getContent(), true);
                $this->parseSubmission($data);
            } catch (\Exception $error) {
                $node = $node->toArray();
                $monolog = new Logger('log');
                $monolog->pushHandler(new StreamHandler(storage_path('logs/log-submission-'.date('Y-m-d').'.txt')), Logger::WARNING);
                $monolog->debug('parsing submission failed', compact('node', 'error'));
            }

            return Response::make(array('success' => true, 'error' => null), 201);
        }

        return Response::make(array('success' => false, 'error' => 'Node for submission could not be saved.'), 500);
    }

    public function parseSubmission($data)
    {
        return \DB::transaction(function ($data) use ($data) {

            /* Create Record */

            $record = new PRRecord();

            $record->basic_data = json_encode($data['basicData']);
            $record->incomplete_reason = $data['incompleteReason'];

            $record->time_tracked = $data['recordedTime'];
            $record->time_spent_patient = $data['totalTimePatient'];
            $record->time_spent_questionnaire = $data['totalTimeQuestionnaire'];

            $record->user = $data['user'];
            $record->language = $data['basicData']['Language'];
            $record->start_date = $data['startDate'];

            $record->ward_name = $data['ward']['name'];
            $record->ward_node_id = $data['ward']['id'];
            $record->hospital_node_id = $data['ward']['hospitalId'];

            $record->save();

            foreach ($data['concerns'] as $concernData) {
                $concern = new PRConcern();

                $concern->serious_answer = $concernData['seriousAnswer'];
                $concern->prevent_answer = $concernData['preventAnswer'];

                if ($noteData = $concernData['whatNote']) {
                    $note = new PRNote();
                    $note->text = $noteData['text'];
                    $note->prase_record_id = $record->id;
                    $note->ward_name = $concernData['ward']['name'];
                    $note->ward_node_id = $concernData['ward']['id'];
                    $note->hospital_node_id = $concernData['ward']['hospitalId'];
                    $note->save();

                    $concern->prase_note_id = $note->id;
                }
                $concern->prase_record_id = $record->id;

                $concern->ward_name = $concernData['ward']['name'] ?: $record->ward_name;
                $concern->ward_node_id = $concernData['ward']['id'] ?: ($record->ward_name ? '' : $record->ward_node_id);
                $concern->hospital_node_id = $concernData['ward']['hospitalId'] ?: $record->hospital_node_id;

                $concern->save();
            }

            foreach ($data['goodNotes'] as $noteData) {
                $note = new PRNote();
                $note->text = $noteData['text'];
                $note->prase_record_id = $record->id;
                $note->ward_name = $noteData['ward']['name'];
                $note->ward_node_id = $noteData['ward']['id'];
                $note->hospital_node_id = $noteData['ward']['hospitalId'];
                $note->save();
            }

            /* Create Questions */

            foreach ($data['pmos'] as $questionData) {
                $question = new PRQuestion();
                $question->question_node_id = $questionData['questionID'];
                $question->answer_node_id = $questionData['answerID'];
                $question->prase_record_id = $record->id;
                $question->save();

                /* Create Questions */

                if ($noteData = $questionData['somethingGood']) {
                    $note = new PRNote();
                    $note->text = $noteData['text'];
                    $note->ward_name = $noteData['ward']['name'] ?: $record->ward_name;
                    $note->ward_node_id = $noteData['ward']['id'] ?: ($record->ward_name ? '' : $record->ward_node_id);
                    $note->hospital_node_id = $noteData['ward']['hospitalId'] ?: $record->hospital_node_id;
                    $note->prase_record_id = $record->id;
                    $note->prase_question_id = $question->id;
                    $note->save();

                    $question->prase_note_id = $note->id;
                }

                if ($concernData = $questionData['concern']) {
                    $concern = new PRConcern();

                    $concern->serious_answer = $concernData['seriousAnswer'];
                    $concern->prevent_answer = $concernData['preventAnswer'];

                    if ($noteData = $concernData['whatNote']) {
                        $note = new PRNote();
                        $note->text = $noteData['text'];
                        $note->prase_record_id = $record->id;
                        $note->prase_question_id = $question->id;
                        $note->ward_name = $concernData['ward']['name'];
                        $note->ward_node_id = $concernData['ward']['id'];
                        $note->hospital_node_id = $concernData['ward']['hospitalId'];
                        $note->save();

                        $concern->prase_note_id = $note->id;
                    }
                    $concern->prase_question_id = $question->id;
                    $concern->prase_record_id = $record->id;
                    $concern->ward_name = $concernData['ward']['name'];
                    $concern->ward_node_id = $concernData['ward']['id'];
                    $concern->hospital_node_id = $concernData['ward']['hospitalId'];

                    $concern->save();

                    $question->prase_concern_id = $concern->id;
                }
                $question->save();
            }

            return $record->save();

            /* End Record */
        });
    }

    public function emailNode()
    {
        return Response::make('', 200);
    }

    public function node($id)
    {
        if (Request::header('Collection-Token') === null) {
            return Response::make('Collection-Token must be specified for this call.', 400);
        }

        $collection = \App::make('collection');
        $node = Node::whereId($id)->whereCollectionId($collection->id)->first();

        if (!$node) {
            return Response::make('node not found', 404);
        }

        if ($node->published_revision) {
            $node = $this->doExtended($node);

            return Api::makeResponse($node, $node->nodetype->name);
        } else {
            return Response::make('No published nodes', 404);
        }
    }

    private function doExtended($node)
    {
        $published_revision = $node->fetchRevision($node->published_revision);

        foreach ($node->nodetype->columns as $item) {
            if (Input::get('expandChildNodes')) {
                if ($item->category == 'resource' and (isset($item->includeWhenExpanded) and $item->includeWhenExpanded)) {
                    if ($published_revision->{$item->name}) {
                        $resource = @Resource::whereId($published_revision->{$item->name})->first()->toArray();

                        unset($resource['catalogue_id'], $resource['created_at'], $resource['updated_at']);

                        $node->{$item->name} = $resource;
                    }
                } elseif ($item->category == 'nodelookup-multi' and (isset($item->includeWhenExpanded) and $item->includeWhenExpanded)) {
                    $nodes = @Node::whereIn('id', explode(',', $published_revision->{$item->name}))->get();

                    foreach ($nodes as &$_node) {
                        if ($_node) {
                            $_node = $this->doExtended($_node);
                        } else {
                            $_node = '';
                        }
                    }

                    $node->{str_plural($item->name)} = $nodes->toArray();
                } elseif ($item->category == 'nodelookup' and (isset($item->includeWhenExpanded) and $item->includeWhenExpanded)) {
                    if ($published_revision->{$item->name}) {
                        $nodes = @Node::whereId($published_revision->{$item->name})->first();
                        if ($nodes) {
                            $node->{$item->name} = $this->doExtended($nodes)->toArray();
                        } else {
                            $node->{$item->name} = '';
                        }
                    } else {
                        $node->{$item->name} = '';
                    }
                } elseif ($item->category == 'userlookup-multi' and (isset($item->includeWhenExpanded) and $item->includeWhenExpanded)) {
                    $users = @User::whereIn('id', explode(',', $published_revision->{$item->name}))->get();

                    foreach ($users as &$_user) {
                        $_user = $_user->toArray();
                    }

                    $node->{str_plural($item->name)} = $users->toArray();
                } elseif ($item->category == 'userlookup' and (isset($item->includeWhenExpanded) and $item->includeWhenExpanded)) {
                    $user = @User::whereId($published_revision->{$item->name})->first();
                    $node->{$item->name} = $user->toArray();
                } else {
                    $node->{$item->name} = $published_revision->{$item->name};
                }
            } else {
                $node->{$item->name} = $published_revision->{$item->name};
            }

            if ($item->category == 'date') {
                $node->{$item->name} = Api::convertDate($published_revision->{$item->name});
            }
        }

        return $node;
    }
}
