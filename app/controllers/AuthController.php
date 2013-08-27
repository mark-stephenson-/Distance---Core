<?php

class AuthController extends BaseController
{
    public function showLogin()
    {
        return View::make('auth.login');
    }

    public function processLogin()
    {
        $validator = new Core\Validators\Login;

        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator->messages())
                ->withInput();
        }

        $bag = new MessageBag();

        try
        {
            $credentials = array(
                'email' => Input::get('email'),
                'password' => Input::get('password'),
            );

            if (Sentry::authenticate($credentials, Input::get('remember'))) {

                if (!Sentry::getUser()->hasAccess('cms.generic.login')) {
                    $bag->add('login', 'You do not have permission to access this.');
                    return Redirect::back()
                            ->withErrors($bag)
                            ->withInput();
                }

                $bag->add('login', 'You have successfully logged in');

                if ($session = Session::get('afterLogin')) {
                    Session::forget('afterLogin');
                    return Redirect::to($session)
                            ->with('successes', $bag);
                } else {
                    return Redirect::route('root')
                            ->with('successes', $bag);
                }

            }
            else
            {
                $bag->add('invalid', 'Invalid login details');
            }
        }
        catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
        {
            $bag->add('email', 'The email field is required');
        }
        catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
        {
            $bag->add('password', 'The password field is required');
        }
        catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
        {
            $bag->add('noexist', 'Invalid login details');
        }
        catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
        {
            $bag->add('active', 'Your account is not active');
        }

        return Redirect::back()
                ->withErrors($bag)
                ->withInput();
    }

    public function processLogout()
    {
        Sentry::logout();

        return Redirect::route('login')->with('successes', new MessageBag(array( "You have been successfully logged out." )));
    }

    public function forgotPassword()
    {
        return View::make('auth.forgot-password-step-one');
    }

    public function processForgotPassword()
    {
        // Let's run the validator
        $validator = new Core\Validators\ForgotPasswordStepOne;

        // If the validator fails
        if ($validator->fails()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }

        try {
            $user = Sentry::getUserProvider()->findByLogin( Input::get('email') );

            $resetCode = $user->getResetPasswordCode();

            Mail::send('emails.password-reset-step-one', compact('user', 'resetCode'), function($message) use ($user)
            {
                $message->to($user->email, $user->full_name)
                    ->subject(Config::get('core.site_name' . ' - Password Reset'))
                    ->from(Config::get('core.emails.send_from'));
            });

            return Redirect::back()
                ->with('successes', new MessageBag(array('If that email is in use by a user, we\'ve sent instructions on how to continue the password reset.')) );

        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            // That user has not been found, but we don't want to tell them!
            return Redirect::back()
                ->with('successes', new MessageBag(array('If that email is in use by a user, we\'ve sent instructions on how to continue the password reset.')) );
        }
    }

    public function resetPassword($user_id, $code)
    {
        try {
            // Find the user using the user id
            $user = Sentry::getUserProvider()->findById($user_id);

            // Check if the reset password code is valid
            if ($user->checkResetPasswordCode($code)) {
                return View::make('auth.forgot-password-step-two', compact('user'));
            } else {
                return Redirect::route('forgot-password')
                    ->withErrors(array('Sorry, there was an issue with that reset request. Please try again.'));
            }
        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            return Redirect::route('forgot-password')
                ->withErrors(array('Sorry, there was an issue with that reset request. Please try again.'));
        }
    }

    public function processResetPassword($user_id, $code)
    {
        try {
            // Find the user using the user id
            $user = Sentry::getUserProvider()->findById($user_id);

            // Check if the reset password code is valid
            if ($user->checkResetPasswordCode($code)) {
                // Let's run the validator
                $validator = new Core\Validators\ForgotPasswordStepTwo;

                // If the validator fails
                if ($validator->fails()) {
                    return Redirect::back()
                        ->withErrors($validator->messages());
                }

                if ($user->attemptResetPassword($code, Input::get('password'))) {
                    Mail::send('emails.password-reset-complete', compact('user', 'resetCode'), function($message) use ($user)
                    {
                        $message->to($user->email, $user->full_name)
                            ->subject(Config::get('core.site_name' . ' - Password Reset Complete'))
                            ->from(Config::get('core.emails.send_from'));
                    });

                    return Redirect::route('login')
                        ->with('successes', new MessageBag(array('Your password has been reset successfully. You can now use it to login.')));
                } else {
                    return Redirect::back()
                        ->withErrors(array('Sorry, there was an issue resetting your password. Please try again.'));
                }
            } else {
                return Redirect::route('forgot-password')
                    ->withErrors(array('Sorry, there was an issue with that reset request. Please try again.'));
            }
        } catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
            return Redirect::route('forgot-password')
                ->withErrors(array('Sorry, there was an issue with that reset request. Please try again.'));
        }
    }
}