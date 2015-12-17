<?php

use Core\Services\NodeService;

class ManageController extends BaseController
{
    protected $nodeService;

    protected $trustNodeType = 2;
    protected $hospitalNodeType = 3;
    protected $wardNodeType = 4;

    public function __construct(NodeService $nodeService)
    {
        parent::__construct();
        $this->nodeService = $nodeService;
    }

    public function index()
    {
        $trusts = Node::whereNodeType($this->trustNodeType)->get();

        return View::make('manage.trusts-index', compact('trusts'));
    }

    public function createTrust()
    {
        $trust = new Node;

        return View::make('manage.trusts-form', compact('trust'));
    }

    public function storeTrust()
    {
        $validator = new Core\Validators\ManageTrust;

        $trustNameValidation = $this->nodeService->checkForUniquenessInType($this->trustNodeType, 'name', Input::get('name'));

        // If the validator or the required column check fails
        if ($validator->fails() or $trustNameValidation) {

            if ($trustNameValidation) {
                $validator->messages()->add("trust-name", $trustNameValidation);
            }

            return Redirect::refresh()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $this->nodeService->createNodeOfTypeWithData($this->trustNodeType, Str::slug(Input::get('name')), Input::only(array('name')));

        return Redirect::route('manage.index')
                ->with('successes', new MessageBag(array('The trust ' . Input::get('name') . ' has been created.')));
    }

    public function trust($trustId) {

        $trust = Node::find($trustId);

        $hospitals = Node::whereNodeType($this->hospitalNodeType)
            ->join("node_type_{$this->hospitalNodeType}", 'nodes.id', '=', "node_type_{$this->hospitalNodeType}.node_id")
            ->where("node_type_{$this->hospitalNodeType}.trust", $trustId)
            ->get(['nodes.*', 'trust']);

        return View::make('manage.hospitals-index', compact('hospitals', 'trust'));
    }

    public function createHospital()
    {
        $hospital = new Node;

        return View::make('manage.hospitals-form', compact('hospital'));
    }

    public function storeHospital($trustId)
    {
        $validator = new Core\Validators\ManageHospital;

        $trustNameValidation = $this->nodeService->checkForUniquenessInType($this->hospitalNodeType, 'name', Input::get('name'));

        // If the validator or the required column check fails
        if ($validator->fails() or $trustNameValidation) {

            if ($trustNameValidation) {
                $validator->messages()->add("hospital-name", $trustNameValidation);
            }

            return Redirect::refresh()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $this->nodeService->createNodeOfTypeWithData($this->hospitalNodeType, Str::slug(Input::get('name')), Input::only(array('name')) + ['trust' => $trustId]);

        return Redirect::route('manage.trust.index', array($trustId))
                ->with('successes', new MessageBag(array('The hospital ' . Input::get('name') . ' has been created.')));
    }

    public function hospital($trustId, $hospitalId) {

        $trust = Node::find($trustId);
        $hospital = Node::find($hospitalId);

        $wards = Node::whereNodeType($this->wardNodeType)
            ->join("node_type_{$this->wardNodeType}", 'nodes.id', '=', "node_type_{$this->wardNodeType}.node_id")
            ->where("node_type_{$this->wardNodeType}.hospital", $hospitalId)
            ->get(['nodes.*', 'hospital']);

        return View::make('manage.wards-index', compact('wards', 'hospital', 'trust'));
    }

    public function createWard()
    {
        $ward = new Node;

        return View::make('manage.wards-form', compact('ward'));
    }

    public function storeWard($trustId, $hospitalId)
    {
        $validator = new Core\Validators\ManageWard;

        $trustNameValidation = $this->nodeService->checkForUniquenessInType($this->wardNodeType, 'name', Input::get('name'));

        // If the validator or the required column check fails
        if ($validator->fails() or $trustNameValidation) {

            if ($trustNameValidation) {
                $validator->messages()->add("ward-name", $trustNameValidation);
            }

            return Redirect::refresh()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $this->nodeService->createNodeOfTypeWithData($this->wardNodeType, Str::slug(Input::get('name')), Input::only(array('name')) + ['hospital' => $hospitalId]);

        return Redirect::route('manage.hospital.index', array($trustId, $hospitalId))
                ->with('successes', new MessageBag(array('The ward ' . Input::get('name') . ' has been created.')));
    }
}