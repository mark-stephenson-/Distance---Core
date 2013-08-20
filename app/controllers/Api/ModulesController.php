<?php namespace Api;

use App, Request, Response, Input;
use Api;

class ModulesController extends \BaseController {
    
    public function modules()
    {
        Input::replace(array(
            'nodeType'          => '14',
            'expandChildNodes'  => true,
        ));
        return App::make('Api\NodeController')->nodes();
    }
}