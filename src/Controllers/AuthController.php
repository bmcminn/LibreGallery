<?php

namespace App\Controllers;

use App\Helpers\Hash;
use App\Helpers\Template;
use App\Helpers\Validator;
use App\Models\Session;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;
use Slim\Routing\RouteCollectorProxy;
use RedBeanPHP\Facade as R;



class AuthController {


    public function login(Request $req, Response $res) {

        $body = $req->getParsedBody();

        $data = Validator::validate($body, [
            [ 'email',      'required|email',   FILTER_SANITIZE_EMAIL           ],
            [ 'password',   'required|min:9',   FILTER_SANITIZE_SPECIAL_CHARS   ],
        ]);

        // if Validator returned a 'messages' field, then we got problems with the data
        if (isset($data['messages'])) {
            return errorResponse($data['messages'], HTTP_BAD_REQUEST);
        }

        $user = R::findOne('user', 'email = ? OR username LIKE ? ', [ $data['email'], $data['email'] ]);

        // TODO: add last_login datetime
        // $user->last_login =


        //
        // HANDLE ERROR STATES
        //

        if (isEmpty($user)) {
            return errorResponse('User could not be found with credentials provided', HTTP_BAD_REQUEST);
        }

        if (!Hash::verify($data['password'], $user->password)) {
            return errorResponse('User could not be found with credentials provided', HTTP_BAD_REQUEST);
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



    public function logout(Request $req, Response $res) {

        // TODO: add log entry of attempted logout of mismatching IP ADDRESSES

        Session::destroy();

        $data = [];

        return jsonResponse($data, 200);
    }


    public function register(Request $req, Response $res) {

        $body = $req->getParsedBody();

        $data = Validator::validate($body, [
            [ 'email',              'required|email',   FILTER_SANITIZE_EMAIL           ],
            [ 'password',           'required|min:9',   FILTER_SANITIZE_SPECIAL_CHARS   ],
            [ 'password',           'required|min:8',               'htmlspecialchars' ],
            [ 'password_confirm',   'required|same:password',       'htmlspecialchars' ],
        ]);

    }


}
