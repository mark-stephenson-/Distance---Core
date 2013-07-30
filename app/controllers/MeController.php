<?php

class MeController extends BaseController
{
    public function index()
    {
        $user = Sentry::getUser();

        return View::make('me.index', compact('user'));
    }

    public function update()
    {
        $validator = new Core\Validators\User;

        if ( Input::get('password') ) {
            $validator->requirePassword();
        }

        if ( $validator->fails() ) {
            return Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }

        if ( Sentry::getUser()->checkPassword( Input::get('current_password')) ) {

            $user = Sentry::getUser();

            $user->first_name = Input::get('first_name');
            $user->last_name = Input::get('last_name');
            $user->email = Input::get('email');

            if ( Input::get('password') ) {
                $user->password = Input::get('password');   
            }

            if ( ! $user->save() ) {
                return Redirect::back()
                    ->withInput()
                    ->withErrors('Sorry, there was an issue saving your changes. Please try again.');
            }

            return Redirect::back()
                    ->with('successes', new MessageBag(array('Your changes have been saved.')));
        } else {
            return Redirect::back()
                ->withInput()
                ->withErrors('Sorry, the "Current Password" you entered is incorrect.');
        }
    }
}