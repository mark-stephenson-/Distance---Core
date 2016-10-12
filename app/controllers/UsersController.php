<?php

class UsersController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $users = Sentry::getUserProvider()->findAll();

        // Strip out the core admins if they are not a core admin...
        if (!Sentry::getUser()->isSuperUser()) {
            $users = array_filter($users, function($user)
            {
                return !$user->isSuperUser();
            });
        }

        return View::make('users.index', compact('users'));
    }

    public function create()
    {
        $user = new User;

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

        return Redirect::route('users.edit', array($user->id))
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

        $groups = array_filter(Sentry::getGroupProvider()->findAll(), function($group) {
            return $group->hierarchy > Sentry::getUser()->topMostGroupHierarchy();
        });

        $trusts = Node::whereNodeTypeIs($this->trustNodeType, 'published')->whereUserHasAccess('manage-trust')->get();
        $trusts->each(function (&$trust) {
            $trust->hospitals = Node::whereNodeTypeIs($this->hospitalNodeType, 'published')
                ->whereUserHasAccess('manage-trust')
                ->where('trust', $trust->node_id)->get();

            $trust->hospitals->each(function (&$hospital) {
                $hospital->wards = Node::whereNodeTypeIs($this->wardNodeType, 'published')
                    ->whereUserHasAccess('manage-trust')
                    ->where('hospital', $hospital->node_id)->get();
            });
        });

        return View::make('users.form', compact('user', 'groups', 'trusts'));
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

        if (Input::get('super_admin') and Sentry::getUser()->isSuperUser()) {
            $user->permissions = array(
                'superuser' => true
            );
        } else {
            $user->permissions = array(
                'superuser' => false
            );
        }

        // node access permissions
        $user->accessible_nodes = '';
        if(Input::get('node_ids')) {
            $user->accessible_nodes = json_encode(Input::get('node_ids'));
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

        return Redirect::route('users.edit', array($user->id))
                ->with('successes', new MessageBag(array($user->fullName . ' has been updated.')));
    }

    public function doAddGroup($user_id, $group_id) {
         try {
            // Check the user exists
            $user = Sentry::getUserProvider()->findById($user_id);

            // Check the group exists
            $group = Sentry::getGroupProvider()->findById($group_id);

            // Add the user to the group
            $user->addGroup($group);

            return Redirect::back()
                ->with('successes', new MessageBag(array('This user has been added to the <b>' . $group->name .'</b> group.' )));            

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {

            return Redirect::back()
                ->withErrors(new MessageBag(array('That user could not be found.' )));

        } catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
            return Redirect::back()
                ->withErrors(new MessageBag(array('That group could not be found.' )));
        }
    }

    public function doRemoveGroup($user_id, $group_id) {
        try {
            // Check the user exists
            $user = Sentry::getUserProvider()->findById($user_id);

            // Check the group exists
            $group = Sentry::getGroupProvider()->findById($group_id);

            // Add the user to the group
            $user->removeGroup($group);

            return Redirect::back()
                ->with('successes', new MessageBag(array('This user has been removed from the <b>' . $group->name .'</b> group.' )));

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {

            return Redirect::back()
                ->withErrors(new MessageBag(array('That user could not be found.' )));

        } catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
            return Redirect::back()
                ->withErrors(new MessageBag(array('That group could not be found.' )));
        }
    }

    public function delete($userId)
    {
        $currentUser = Sentry::getUser();

        $user = Sentry::findUserById($userId);

        $userNodes = Node::whereOwnedBy($userId)->get();

        foreach($userNodes as $node) {
            $node->owned_by = $node->created_by = $currentUser->id;
            $node->save();
        }

        $user->delete();

        return Redirect::route('users.index')->with('successes', new MessageBag(array('This user has been removed.')));
    }

    public function getGroups($userId)
    {
        $user = Sentry::getUserProvider()->findById($userId);

        $groups = Sentry::getGroupProvider()->findAll();

        $groups = array_filter($groups, function($group) {
            return $group->hierarchy >= Sentry::getUser()->topMostGroupHierarchy();
        });

        return View::make('users.groups', compact('user', 'groups'));
    }

}