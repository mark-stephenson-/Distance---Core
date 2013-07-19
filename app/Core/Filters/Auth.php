<?php
namespace Core\Filters;

use Sentry;

class Auth
{

    public function auth()
    {
        if (!Sentry::check()) {
            Session::flash('errors', new MessageBag(array( 'You need to be logged in to do that.' )));
            Session::put('afterLogin', URL::current());
        
            return Redirect::route('login');
        }
    }

    /**
     * Used to verify a user has the permission 'core-admin'
     */
    public function coreAdmin()
    {
        if (Sentry::check() and !Sentry::getUser()->hasAccess('core-admin') ) {
            Session::flash('errors', new MessageBag(array( 'You do not have permission to access this.' )));
            Session::put('afterLogin', URL::current());
        
            return Redirect::to('/');
        }
    }

    public function superAdmin()
    {
        if (Sentry::check() and !Sentry::getUser()->hasAccess('super-admin') ) {
            Session::flash('errors', new MessageBag(array( 'You do not have permission to access this.' )));
            Session::put('afterLogin', URL::current());
        
            return Redirect::to('/');
        }
    }

}