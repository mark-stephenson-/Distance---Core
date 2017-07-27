<?php

use Core\Services\NodeService;

class ManageController extends BaseController
{
    protected $nodeService;

    public function __construct(NodeService $nodeService)
    {
        parent::__construct();
        $this->nodeService = $nodeService;
    }

    public function index()
    {
        $trusts = Node::isPublished()->whereNodeType($this->trustNodeType, 'published')->whereUserHasAccess('manage-trust')->get();

        return View::make('manage.trusts-index', compact('trusts'));
    }

    public function createTrust()
    {
        $trust = new Node();

        return View::make('manage.trusts-form', compact('trust'));
    }

    public function storeTrust()
    {
        $validator = new Core\Validators\ManageTrust();

        $trustNameValidation = $this->nodeService->checkForUniquenessInType($this->trustNodeType, 'name', Input::get('name'));

        // If the validator or the required column check fails
        if ($validator->fails() or $trustNameValidation) {
            if ($trustNameValidation) {
                $validator->messages()->add('trust-name', $trustNameValidation);
            }

            return Redirect::refresh()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $this->nodeService->createPublishedNodeOfTypeWithData($this->trustNodeType, Str::slug(Input::get('name')), Input::only(array('name')));

        return Redirect::route('manage.index')
                ->with('successes', new MessageBag(array('The trust '.Input::get('name').' has been created.')));
    }

    public function editTrust($trustId)
    {
        $trust = Node::find($trustId);

        return View::make('manage.trusts-form', compact('trust'));
    }

    public function updateTrust($trustId)
    {
        $trust = Node::find($trustId);
        $validator = new Core\Validators\ManageTrust();

        $trustNameValidation = $this->nodeService->checkForUniquenessInType($this->trustNodeType, 'name', Input::get('name'));

        // If the validator or the required column check fails
        if ($validator->fails() or $trustNameValidation) {
            if ($trustNameValidation) {
                $validator->messages()->add('trust-name', $trustNameValidation);
            }

            return Redirect::refresh()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $this->nodeService->updatePublishedNodeWithData($trust, Input::only(array('name')));

        return Redirect::route('manage.index')
                ->with('successes', new MessageBag(array('The trust '.Input::get('name').' has been updated.')));
    }

    public function trust($trustId)
    {
        $trust = Node::find($trustId);

        $hospitals = Node::isPublished()->whereNodeType($this->hospitalNodeType, 'published')->whereUserHasAccess('manage-trust')
            ->join("node_type_{$this->hospitalNodeType}", 'nodes.id', '=', "node_type_{$this->hospitalNodeType}.node_id")
            ->where("node_type_{$this->hospitalNodeType}.trust", $trustId)
            ->where("node_type_{$this->hospitalNodeType}.status", 'published')
            ->get(['nodes.*', 'trust']);

        return View::make('manage.hospitals-index', compact('hospitals', 'trust'));
    }

    public function createHospital()
    {
        $hospital = new Node();

        return View::make('manage.hospitals-form', compact('hospital'));
    }

    public function storeHospital($trustId)
    {
        $validator = new Core\Validators\ManageHospital();

        $trustNameValidation = $this->nodeService->checkForUniquenessInType($this->hospitalNodeType, 'name', Input::get('name'));

        // If the validator or the required column check fails
        if ($validator->fails() or $trustNameValidation) {
            if ($trustNameValidation) {
                $validator->messages()->add('hospital-name', $trustNameValidation);
            }

            return Redirect::refresh()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $this->nodeService->createPublishedNodeOfTypeWithData($this->hospitalNodeType, Str::slug(Input::get('name')), Input::only(array('name')) + ['trust' => $trustId]);

        return Redirect::route('manage.trust.index', array($trustId))
                ->with('successes', new MessageBag(array('The hospital '.Input::get('name').' has been created.')));
    }

    public function editHospital($trustId, $hospitalId)
    {
        $hospital = Node::find($hospitalId);

        return View::make('manage.hospitals-form', compact('hospital'));
    }

    public function updateHospital($trustId, $hospitalId)
    {
        $hospital = Node::find($hospitalId);
        $validator = new Core\Validators\ManageHospital();

        $trustNameValidation = $this->nodeService->checkForUniquenessInType($this->hospitalNodeType, 'name', Input::get('name'));

        // If the validator or the required column check fails
        if ($validator->fails() or $trustNameValidation) {
            if ($trustNameValidation) {
                $validator->messages()->add('hospital-name', $trustNameValidation);
            }

            return Redirect::refresh()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $this->nodeService->updatePublishedNodeWithData($hospital, Input::only(array('name')));

        return Redirect::route('manage.trust.index', array($trustId))
                ->with('successes', new MessageBag(array('The hospital '.Input::get('name').' has been updated.')));
    }

    public function hospital($trustId, $hospitalId)
    {
        $trust = Node::find($trustId);
        $hospital = Node::find($hospitalId);

        $wards = Node::isPublished()->whereNodeType($this->wardNodeType, 'published')->whereUserHasAccess('manage-trust')
            ->join("node_type_{$this->wardNodeType}", 'nodes.published_revision', '=', "node_type_{$this->wardNodeType}.id")
            ->where("node_type_{$this->wardNodeType}.hospital", $hospitalId)
            ->where("node_type_{$this->wardNodeType}.status", 'published')
            ->get(['nodes.*', 'hospital']);

        return View::make('manage.wards-index', compact('wards', 'hospital', 'trust'));
    }

    public function createWard()
    {
        $ward = new Node();

        return View::make('manage.wards-form', compact('ward'));
    }

    public function storeWard($trustId, $hospitalId)
    {
        $validator = new Core\Validators\ManageWard();

        $trustNameValidation = $this->nodeService->checkForUniquenessInType($this->wardNodeType, 'name', Input::get('name'));

        // If the validator or the required column check fails
        if ($validator->fails() or $trustNameValidation) {
            if ($trustNameValidation) {
                $validator->messages()->add('ward-name', $trustNameValidation);
            }

            return Redirect::refresh()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $this->nodeService->createPublishedNodeOfTypeWithData($this->wardNodeType, Str::slug(Input::get('name')), Input::only(array('name')) + ['hospital' => $hospitalId]);

        return Redirect::route('manage.hospital.index', array($trustId, $hospitalId))
                ->with('successes', new MessageBag(array('The ward '.Input::get('name').' has been created.')));
    }

    public function editWard($trustId, $hospitalId, $wardId)
    {
        $ward = Node::find($wardId);

        return View::make('manage.wards-form', compact('ward'));
    }

    public function updateWard($trustId, $hospitalId, $wardId)
    {
        $ward = Node::find($wardId);
        $validator = new Core\Validators\ManageTrust();

        $trustNameValidation = $this->nodeService->checkForUniquenessInType($this->wardNodeType, 'name', Input::get('name'));

        // If the validator or the required column check fails
        if ($validator->fails() or $trustNameValidation) {
            if ($trustNameValidation) {
                $validator->messages()->add('trust-name', $trustNameValidation);
            }

            return Redirect::refresh()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $wardChangeComment = Input::get('change_comment');

        if ($wardChangeComment == '') {
            $wardChangeComment = 'Renamed '.Input::get('old_name').' to '.Input::get('name');
        }

        // We will update the old ward with the comment, and then create a new one
        $this->nodeService->updatePublishedNodeWithData($ward, ['ward-change-comment' => $wardChangeComment]);
        $ward->markAsRetired($ward->published_revision);

        $this->nodeService->createPublishedNodeOfTypeWithData($this->wardNodeType, Str::slug(Input::get('name')), Input::only(array('name')) + ['hospital' => $hospitalId]);

        return Redirect::route('manage.hospital.index', array($trustId, $hospitalId))
                ->with('successes', new MessageBag(array('The ward '.Input::get('name').' has been updated.')));
    }

    public function mergeWard($trustId, $hospitalId, $wardId)
    {
        $ward = Node::find($wardId);
        $possibleMerges = Node::isPublished()->whereNodeType($this->wardNodeType)
            ->join("node_type_{$this->wardNodeType}", 'nodes.id', '=', "node_type_{$this->wardNodeType}.node_id")
            ->where("node_type_{$this->wardNodeType}.hospital", $hospitalId)
            ->where('nodes.id', '!=', $wardId)
            ->get(['nodes.*', 'hospital', "node_type_{$this->wardNodeType}.name AS ward_name"]);

        return View::make('manage.wards-merge', compact('ward', 'possibleMerges'));
    }

    public function performMergeWard($trustId, $hospitalId, $wardId)
    {
        $ward = Node::find($wardId);
        $destWard = Node::find(Input::get('chosen_ward'));

        $wardChangeComment = Input::get('change_comment');

        if ($wardChangeComment == '') {
            $wardChangeComment = 'Merged '.$ward->latestRevision()->name.' to '.$destWard->latestRevision()->name;
        }

        // We will update the old ward with the comment, and then create a new one
        $this->nodeService->updatePublishedNodeWithData($ward, ['ward-change-comment' => $wardChangeComment]);
        $this->nodeService->updatePublishedNodeWithData($destWard, ['ward-change-comment' => $wardChangeComment]);
        $ward->markAsRetired($ward->published_revision);
        $destWard->markAsRetired($destWard->published_revision);

        $this->nodeService->createPublishedNodeOfTypeWithData($this->wardNodeType, Str::slug($destWard->latestRevision()->name), ['name' => $wardChangeComment, 'hospital' => $hospitalId]);

        return Redirect::route('manage.hospital.index', array($trustId, $hospitalId))
                ->with('successes', new MessageBag(array('The ward '.$ward->latestRevision()->name.' has been merged into '.$destWard->latestRevision()->name)));
    }

    public function deleteWard($trustId, $hospitalId, $wardId)
    {
        $ward = Node::find($wardId);

        return View::make('manage.wards-delete', compact('ward'));
    }

    public function performDeleteWard($trustId, $hospitalId, $wardId)
    {
        $ward = Node::find($wardId);

        $wardChangeComment = Input::get('change_comment');


        if ($wardChangeComment == '') {
            $wardChangeComment = 'Deleted '.$ward->latestRevision()->name;
        }

        $this->nodeService->updatePublishedNodeWithData($ward, ['ward-change-comment' => $wardChangeComment]);
        $ward->markAsRetired($ward->published_revision);

        return Redirect::route('manage.hospital.index', array($trustId, $hospitalId))
                ->with('successes', new MessageBag(array('The ward '.$ward->latestRevision()->name.' has been deleted.')));
    }
}
