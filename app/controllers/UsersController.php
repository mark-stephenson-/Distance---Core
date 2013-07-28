<?php

class UsersController extends BaseController
{

    public function __construct()
    {
        parent::__construct();

        $this->beforeFilter(function() {

            if (!Sentry::getUser()->hasAccess('cms.users.*')) {
                die("no access");
            }

        });

        $this->beforeFilter(function() {

            if (!Sentry::getUser()->hasAccess('cms.users.create')) {
                die("no access");
            }

        }, ['only' => ['create', 'store']]);

        $this->beforeFilter(function() {

            if (!Sentry::getUser()->hasAccess('cms.users.update')) {
                die("no access");
            }

        }, ['only' => ['edit', 'update']]);

        $this->beforeFilter(function() {

            if (!Sentry::getUser()->hasAccess('cms.users.delete')) {
                die("no access");
            }

        }, ['only' => ['delete']]);
    }

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
        $validator->requirePassword();

        // If the validator fails
        if ($validator->fails()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }

        try {
            $user = Sentry::getUserProvider()->create(array(
                'email'         => Input::get('email'),
                'password'      => Input::get('password'),
                'first_name'    => Input::get('first_name'),
                'last_name'     => Input::get('last_name'),
                'activated'     => 1,
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

    public function edit($userId)
    {
        try
        {
            $user = Sentry::getUserProvider()->findById($userId);
        }
        catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            return Redirect::back()
                ->withErrors(new MessageBag(array('That user could not be found.' )));
        }

        $groups = Sentry::getGroupProvider()->findAll();

        return View::make('users.form', compact('user', 'groups'));
    }

    public function update($userId)
    {
        try
        {
            $user = Sentry::getUserProvider()->findById($userId);
        }
        catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            return Redirect::back()
                ->withErrors(new MessageBag(array('That user could not be found.' )));
        }

        // Let's run the validator
        $validator = new Core\Validators\User(null, false);

        // If the validator fails
        if ($validator->fails()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }

        $user->email = Input::get('email');
        $user->first_name = Input::get('first_name');
        $user->last_name = Input::get('last_name');
        $user->bio = Input::get('bio');

        if (Config::get('core.labels.user_field_1')) {
            $user->field_1 = Input::get('field_1');
        }

        if (Config::get('core.labels.user_field_2')) {
            $user->field_2 = Input::get('field_2');
        }

        if (Config::get('core.labels.user_field_3')) {
            $user->field_3 = Input::get('field_3');
        }

        if (Config::get('core.labels.user_field_4')) {
            $user->field_4 = Input::get('field_4');
        }

        if (Config::get('core.labels.user_field_5')) {
            $user->field_5 = Input::get('field_5');
        }

        if (Input::get('password')) {
            $user->password = Input::get('password');
        }

        try {
            $user->save();
        }
        catch (\Cartalyst\Sentry\Users\UserExistsException $e)
        {
            return \Redirect::back()
                ->withInput()
                ->withErrors(new \MessageBag(array("A user with this email already exists on the system, it is likely they exist above your access level or they are an existing reviewer.")));
        }

        return Redirect::route('users.index')
                ->with('successes', new MessageBag(array($user->fullName . ' has been updated.')));
    }
}