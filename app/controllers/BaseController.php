<?php

class BaseController extends Controller {

    public function __construct() {
        $appId = Request::segment(2);
        if (is_numeric($appId) and Request::segment(1) == 'apps') {
            Session::put('current-app', Application::find($appId));
        } else {
            Session::put('current-app', Application::current());
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