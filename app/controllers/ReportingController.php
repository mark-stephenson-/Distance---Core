<?php

class ReportingController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$trusts = DB::table('nodes')
			->join('node_type_2', 'node_id', '=', 'nodes.id')
			->where('node_type', 2)
			->lists('node_type_2.name', 'node_id');

		$trusts = ['' => 'Please select a Trust'] + $trusts;

		return View::make('reporting.index', compact('trusts'));
	}

	public function hospitals($trustId)
	{
		$hospitals = DB::table('nodes')
			->join('node_type_3', 'node_id', '=', 'nodes.id')
			->where('node_type', 3)
			->where('node_type_3.trust', $trustId)
			->lists('node_type_3.name', 'node_id');

		$hospitals = ['' => 'Please select a Hospital'] + $hospitals;

		return json_encode($hospitals);
	}

	public function wards($hospitalId)
	{
		$wards = DB::table('nodes')
			->join('node_type_4', 'node_id', '=', 'nodes.id')
			->where('node_type', 4)
			->where('node_type_4.hospital', $hospitalId)
			->lists('node_type_4.name', 'node_id');

		$wards = ['' => 'Please select a Ward'] + $wards;

		return json_encode($wards);
	}
}
