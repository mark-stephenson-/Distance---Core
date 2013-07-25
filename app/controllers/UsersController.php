<?php

class UsersController extends BaseController
{
    public function index()
    {
        $users = Sentry::getUserProvider()->findAll();

        // Strip out the core admins if they are not a core admin...
        if (!Sentry::getUser()->isSuperUser()) {
            $users = array_filter($users, function($user)
            {
                return $user->isSuperUser();
            });
        }

        return View::make('users.index', compact('users'));
    }
}