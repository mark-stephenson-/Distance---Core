<?php

class BaseController extends Controller {

    public function __construct() {
        $appId = Request::segment(2);
        if ($appId) {
            Session::put('current-app', Application::find($appId));
        }
    }

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

}