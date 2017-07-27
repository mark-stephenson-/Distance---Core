<?php namespace Api;

use Api;
use Input, Response, Sentry;
use UserDevice;

class AuthenticationController extends \BaseController {
    public function authenticate() {
        $contentTypeParts = explode(';', \Request::header('Content-Type'));
        $contentType = $contentTypeParts[0];

        if ( $contentType == "text/xml" ) {
            $input = simplexml_load_string(trim(file_get_contents('php://input')));
        } else if ( $contentType == "application/json" ) {
            $input = json_decode(file_get_contents('php://input'));
        
            if ( json_last_error() ) {
                return Response::make('Error decoding JSON request body', 400);
            }
        } else {
            return Response::make('Content-Type not recognised', 400);
        }

        if ( ! isset($input->email) and ! isset($input->password) ) {
            return Response::make('Email and Password must be supplied in the request body.', 400);
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

                // Let's handle the devices!
                $this->handleDevice($input, $user->id);

                $devices = UserDevice::whereUserId($user->id)->get(array('device_token', 'device_type'))->toArray();
                $collectionController = new CollectionController;

                return Api::makeResponse( array(
                    'usertoken'   => $user->key, 
                    'devices'     => $devices,
                    'collections' => $collectionController->doExtended($user->collections())->toArray()
                ), 'authentication', 200);
            }
        } catch (\Cartalyst\Sentry\Users\WrongPasswordException $e) {
            return Response::make('', 403);
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            return Response::make('', 403);
        }
    } 

    protected function handleDevice($input, $userId) {
        if (isset($input->{'device-token'}) and isset($input->{'device-type'})) {

            $deviceToken = $input->{'device-token'};
            $deviceType = $input->{'device-type'};

            // Do they already exist?
            $exists = UserDevice::where('device_token', '=', $deviceToken)
                                    ->where('user_id', '=', $userId)
                                    ->count();

            if ($exists == 0) {
                // Add it!
                $device = new UserDevice;

                $device->device_token = $deviceToken;
                $device->device_type = $deviceType;
                $device->user_id = $userId;

                $device->save();
            }

        }
    }
}