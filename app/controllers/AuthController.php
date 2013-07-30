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
        return View::make('auth.forgot-password');
    }

    public function processForgotPassword()
    {
        // Let's run the validator
        $validator = new Core\Validators\ForgotPassword;

        // If the validator fails
        if ($validator->fails()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($validator->messages());
        }
    }
}