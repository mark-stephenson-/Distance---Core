<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

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

	public function generate($wardId)
	{
		if (!$wardId or !is_numeric($wardId)) {
			return Response::make("Invalid ward specified.", 400);
		}

		if (!Input::get('startDate') or !$startDate = Carbon::createFromFormat("d-m-Y", Input::get('startDate'))) {
			return Response::make("Invalid start date specified.", 400);
		}

		if (!Input::get('endDate') or !$endDate = Carbon::createFromFormat("d-m-Y", Input::get('endDate'))) {
			return Response::make("Invalid end date specified.", 400);
		}

		dd($wardId, $startDate, $endDate);
	}
}
