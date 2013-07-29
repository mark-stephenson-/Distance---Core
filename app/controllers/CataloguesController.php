<?php

class CataloguesController extends BaseController
{
    

    public function index() {
        $catalogues = Catalogue::with('collections')->get();

        return View::make('catalogues.index', compact('catalogues'));
    }

}