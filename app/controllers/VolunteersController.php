<?php

class VolunteersController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $volunteers = Node::whereNodeType(9)->get();

        return View::make('volunteers.index', compact('volunteers'));
    }

    public function edit($volunteerId)
    {
        $volunteer = Node::find($volunteerId);

        return View::make('volunteers.form', compact('volunteer'));
    }

    public function update($volunteerId)
    {
        $volunteer = Node::find($volunteerId);

        $validator = new Core\Validators\Volunteer;

        $usernameError = $this->checkForUniqueUsernameInWard($volunteerId);

        // If the validator or the required column check fails
        if ($validator->fails() or $usernameError) {

            if ($usernameError) {
                $validator->messages()->add("usernameward", $usernameError);
            }

            return Redirect::refresh()
                ->withInput()
                ->withErrors($validator->messages());
        }
        
        // All good
        $nodeRevision = (array) $volunteer->latestRevision();
        $nodeRevision['updated_at'] = DB::raw('NOW()');

        foreach(Input::all() as $key => $val) {
            if (isset($nodeRevision[$key])) {
                $nodeRevision[$key] = $val;
            }
        }

        unset($nodeRevision['user']);

        $nodeResult = $volunteer->updateDraft($nodeRevision, $nodeRevision['id']);

        $volunteer->touch();

        return Redirect::route('volunteers.index')
                ->with('successes', new MessageBag(array('The volunteer ' . $nodeRevision['username'] . ' has been updated.')));
    }

    public function create()
    {
        $volunteer = new Node;

        return View::make('volunteers.form', compact('volunteer'));
    }

    public function store()
    {
        $volunteer = new Node;

        $validator = new Core\Validators\Volunteer;
        $validator->usernameRequired();

        $usernameError = $this->checkForUniqueUsernameInWard();

        // If the validator or the required column check fails
        if ($validator->fails() or $usernameError) {

            if ($usernameError) {
                $validator->messages()->add("usernameward", $usernameError);
            }

            return Redirect::refresh()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $volunteer->title = Input::get('firstname') . ' ' . Input::get('lastname');
        $volunteer->created_by = $volunteer->owned_by = Sentry::getUser()->id;
        $volunteer->node_type = 9;
        $volunteer->collection_id = CORE_COLLECTION_ID;
        $volunteer->status = 'published';
        $volunteer->save();
        
        // All good
        $nodeRevision = array_fill_keys(array('username', 'password', 'firstname', 'lastname', 'ward'), '');
        $nodeRevision['updated_at'] = DB::raw('NOW()');
        $nodeRevision['status'] = 'published';
        $nodeRevision['created_by'] = $volunteer->created_by;
        $nodeRevision['updated_by'] = $volunteer->created_by;
        $nodeRevision['node_id'] = $volunteer->id;

        foreach(Input::all() as $key => $val) {
            if (isset($nodeRevision[$key])) {
                $nodeRevision[$key] = $val;
            }
        }

        $nodeResult = $volunteer->createDraft($nodeRevision);

        $volunteer->latest_revision = $nodeResult;
        $volunteer->save();

        return Redirect::route('volunteers.index')
                ->with('successes', new MessageBag(array('The volunteer ' . $nodeRevision['username'] . ' has been updated.')));
    }

    protected function checkForUniqueUsernameInWard($existingId = null)
    {
        $username = Input::get('username');
        $ward = Input::get('ward');

        $volunteersInWard = \DB::table('node_type_9')
            ->whereWard($ward)
            ->whereUsername($username);

        if ($existingId) {
            $volunteersInWard->where('node_id', '!=', $existingId);
        }

        if ($volunteersInWard->count() > 0) {
            return "That username already exists within the selected ward.";
        }
    }
}