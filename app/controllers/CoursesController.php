<?php

class CoursesController extends BaseController {

    public function index()
    {
        return View::make('courses.index');
    }
    
}