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

    public function create()
    {
        $user = new User;

        // Check for create permission
        $groups = Sentry::getGroupProvider()->findAll();

        return View::make('users.form', compact('user', 'groups'));
    }

    public function store()
    {
        // Let's run the validator
        $validator = new Core\Validators\User;

        // If the validator fails
        if ($validator->fails()) {
            return \Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }

        try {
            $user = Sentry::getUserProvider()->create(array(
                'email'         => Input::get('email'),
                'password'      => Input::get('password'),
                'first_name'    => Input::get('first_name'),
                'last_name'     => Input::get('last_name'),
            ));
        }
        catch (Cartalyst\Sentry\Users\UserExistsException $e)
        {
            return Redirect::back()
                ->withInput()
                ->withErrors(new MessageBag(array("A user with this email already exists on the system.")));
        }

        return Redirect::route('users.index')
                ->with('successes', new MessageBag(array($user->fullName . ' has been created.')));
    }
}