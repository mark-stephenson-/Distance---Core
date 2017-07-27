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