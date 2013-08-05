<?php namespace Api;

use Api;
use Input, Response, Sentry;

class AuthenticationController extends \BaseController {
    public function authenticate() {
        $input = json_decode(file_get_contents('php://input'));
        
        if ( json_last_error() ) {
            return Response::make('Error decoding JSON request body', 400);
        }
        
        try {
            $user = Sentry::getUserProvider()->findByCredentials(array(
                'email'      => $input->email,
                'password'   => $input->password
            ));

            if ( $user ) {
                // Need to make a user key and update last_accessed
                
                if ( ! $user->key ) {
                    $user->key = sha1($user->email . microtime() . $user->password );
                    $user->save();
                }

                return Api::makeResponse( array('UserToken' => $user->key), 200);
            }
        } catch (\Cartalyst\Sentry\Users\WrongPasswordException $e) {
            return Response::make('', 403);
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            return Response::make('', 403);
        }
    }   
}