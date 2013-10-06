<?php

class BaseController extends Controller {

    public function __construct() {
        $appId = Request::segment(2);
        if (is_numeric($appId) and Request::segment(1) == 'apps') {
            Session::put('current-app', $appId);

            $collectionId = Request::segment(4);
            if (is_numeric($collectionId) and Request::segment(3) == 'collections') {
                Session::put('current-collection', $collectionId);
            } else {
                // This stores a guessed app ID
                Collection::currentId();
            }
        } else {
            // This stores a guessed app ID
            Application::currentId();
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