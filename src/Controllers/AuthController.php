<?php

namespace App\Controllers;

use App\Helpers\Config;
use App\Helpers\Hash;
use App\Helpers\Template;
use App\Helpers\Email;
use App\Helpers\Token;
use App\Helpers\Validator;
use App\Models\Session;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;
use Slim\Routing\RouteCollectorProxy;
use RedBeanPHP\Facade as R;



class AuthController {


    private array $messages = [
        'errorBadCredentials'       => 'User could not be found with credentials provided',
        'passwordResetConfirmation' => 'A password reset email will be sent',
    ];



    public static array $schema = [
        'email'     => [ 'email',       'required|email', FILTER_SANITIZE_EMAIL ],
        'password'  => [ 'password',    'required|min:9', FILTER_SANITIZE_SPECIAL_CHARS ],
    ];


    /**
     * { function_description }
     *
     * @param      Request   $req    The request
     * @param      Response  $res    The resource
     *
     * @return     <type>    ( description_of_the_return_value )
     */
    public function login(Request $req, Response $res) {

        $body = $req->getParsedBody();

        $data = Validator::validate($body, [
            self::$schema['email'],
            self::$schema['password'],
        ]);

        // if Validator returned a 'messages' field, then we got problems with the data
        if (isset($data['messages'])) {
            return errorResponse($data['messages'], HTTP_BAD_REQUEST);
        }

        $user = R::findOne('user', 'email = ? OR username LIKE ? ', [ $data['email'], $data['email'] ]);


        //
        // HANDLE ERROR STATES
        //

        if (isEmpty($user)) {
            return errorResponse($this->messages['errorBadCredentials'], HTTP_BAD_REQUEST);
        }

        if (!Hash::verify($data['password'], $user->password)) {
            return errorResponse($this->messages['errorBadCredentials'], HTTP_BAD_REQUEST);
        }


        //
        // INIT SESSION VALUES
        //

        Session::set('user_id', $user->uuid);
        Session::set('is_admin', !!$user->is_admin);
        Session::set('useragent', getUserAgent());


        //
        // UPDATE USER LOGIN TIME
        //

        $user->last_login = new \DateTime();

        R::store($user);


        //
        // SETUP RESPONSE MODEL
        //

        $model = [];

        $model['user'] = filterJson($user->export(), [
            'id'        => 'int',
            'username'  => 'string',
            'email'     => 'email',
            'username'  => 'string',
            'is_admin'  => 'bool',
        ]);

        return jsonResponse($model);
    }


    /**
     * { function_description }
     *
     * @param      Request   $req    The request
     * @param      Response  $res    The resource
     *
     * @return     <type>    ( description_of_the_return_value )
     */
    public function logout(Request $req, Response $res) {

        // TODO: add log entry of attempted logout of mismatching IP ADDRESSES

        Session::destroy();

        $data = [];

        return jsonResponse($data, 200);
    }


    /**
     * { function_description }
     *
     * @param      Request   $req    The request
     * @param      Response  $res    The resource
     */
    public function register(Request $req, Response $res) {

        $body = $req->getParsedBody();

        $data = Validator::validate($body, [
            [ 'email',              'required|email',   FILTER_SANITIZE_EMAIL           ],
            [ 'password',           'required|min:9',   FILTER_SANITIZE_SPECIAL_CHARS   ],
            [ 'password',           'required|min:8',               'htmlspecialchars' ],
            [ 'password_confirm',   'required|same:password',       'htmlspecialchars' ],
        ]);
    }


    /**
     * { function_description }
     *
     * @param      Request   $req    The request
     * @param      Response  $res    The resource
     */
    public function passwordReset(Request $req, Response $res) {

        $body = $req->getParsedBody();

        $data = Validator::validate($body, [
            self::$schema['email'],
        ]);


        if (isset($data['messages'])) {
            return errorResponse($data['messages'], HTTP_BAD_REQUEST);
        }

        $user = R::findOne('user', 'email = ? OR username LIKE ? ', [ $data['email'], $data['email'] ]);


        // lie to the user cuz we don't need to leak that the user credentials are valid or invalid
        if (isEmpty($user)) {
            return errorResponse($this->messages['passwordResetConfirmation'], HTTP_SUCCESS);
        }


        // create a password reset token in database we send to the users email address
        $token = Token::generateSHA256([
            'secret' => 'PasswordResetTokenSecretPhraseHere'
        ]);

        // generate a token record
        try {
            $bean = R::dispense('token');

            $bean->token    = $token;
            $bean->type     = Token::TYPE_PASSWORD_RESET;
            $bean->user_id  = $user->uuid;

            $id = R::store($bean);

        } catch(\Exception $err) {
            return errorResponse($err->getMessage(), HTTP_SERVER_ERROR);

        }


        // EMAIL THE LINK FOR THE USER

        $model = Config::get();

        $model['token'] = $token;
        $model['user'] = $user->export();

        $model['passwordResetUrl'] = implode('', [
            $model['app']['url'],
            '?token=',
            $token
        ]);

        $emailSent = Email::sendMessage($model);

        if (!$emailSent) {
            return errorResponse('Email not sent', HTTP_SERVER_ERROR);
        }


        return jsonResponse([ 'message' => '' ]);
    }


    /**
     * { function_description }
     *
     * @param      Request   $req    The request
     * @param      Response  $res    The resource
     */
    public function verifyPasswordReset(Request $req, Response $res) {


    }


}
