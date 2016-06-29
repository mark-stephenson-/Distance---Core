<?php

class ReportingController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$trusts = DB::table('nodes')->where('node_type', 2)->lists('title', 'id');
        array_unshift($trusts, 'Please select a Trust');
		return View::make('reporting.index', compact('trusts'));
	}
}
