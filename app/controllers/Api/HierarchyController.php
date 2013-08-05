<?php namespace Api;

use collection;

class HierarchyController extends \BaseController {

    public function hierarchy()
    {
        return Collection::whereId( \App::make('collection')->id )->first()->hierarchy;
    }
}