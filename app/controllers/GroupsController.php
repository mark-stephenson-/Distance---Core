<?php

class GroupsController extends BaseController
{
    public function index()
    {
        $groups = Group::with('users')->get();

        return View::make('groups.index', compact('groups'));
    }

    public function create()
    {
        $group = new Group;
        $permissions = Permission::tree($group, Application::get());

        return View::make('groups.form', compact('group', 'permissions'));
    }

    public function store()
    {
        // Let's run the validator
        $validator = new Core\Validators\Group;

        // If the validator fails
        if ($validator->fails()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }

        try
        {
            // Create the group
            $group = Sentry::getGroupProvider()->create(array(
                'name'        => Input::get('name'),
                'permissions' => Input::get('permissions', array()),
            ));
        }
        catch (Cartalyst\Sentry\Groups\GroupExistsException $e)
        {
            return Redirect::back()
                ->withInput()
                ->withErrors(new MessageBag(array("A group with this name already exists.")));
        }

        $group->users()->sync(Input::get('members', array()));

        return Redirect::route('groups.index')
                ->with('successes', new MessageBag(array($group->name . ' has been created.')));
    }

    public function edit($groupId)
    {
        $group = Group::find($groupId);
        $permissions = Permission::tree($group, Application::get());

        return View::make('groups.form', compact('group', 'permissions'));
    }

    public function update($groupId)
    {
        $group = Group::find($groupId);

        // Let's run the validator
        $validator = new Core\Validators\Group;

        // If the validator fails
        if ($validator->fails()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }

        try
        {
            $group->name = Input::get('name');
            $group->permissions = Input::get('permissions', array());

            foreach (array_diff_key($group->getPermissions(), Input::get('permissions') ?: array()) as $key => $value) {
                $group->permissions = array($key => 0);
            }

            $group->users()->sync(Input::get('members', array()));
            $group->save();
        }
        catch (Cartalyst\Sentry\Groups\GroupExistsException $e)
        {
            return Redirect::back()
                ->withInput()
                ->withErrors(new MessageBag(array("A group with this name already exists.")));
        }

        return Redirect::route('groups.index')
                ->with('successes', new MessageBag(array($group->name . ' has been updated.')));
    }
}