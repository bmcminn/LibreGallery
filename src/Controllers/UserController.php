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



class UserController extends BaseController {

    protected $table = 'user';

    protected $pageLimit = 10;

    protected $modelSchema = [
        'id'            => 'int',
        'uuid'          => 'string',
        'email'         => 'email',
        'is_admin'      => 'bool',
        'created_at'    => 'datetime',
        'deleted_at'    => 'datetime',
        'last_login'    => 'datetime',
    ];


    public function getUsers(Request $req, Response $res, array $params) {

        $body = $req->getParsedBody();

        $index = $params['index'] ?? 0;

        $limit = $this->pageLimit;

        $offset = $index * $limit;

        $userCount = R::count('user');

        $users = $this->readAll("LIMIT ? OFFSET ?", [ $limit, $offset ]);

        $model = [];

        $model['pagination'] = $this->paginate($index, $limit, $userCount);

        $model['users'] = R::exportAll($users);

        foreach ($model['users'] as $i => $user) {
            $model['users'][$i] = $this->normalizeUser($user);
        }

        return jsonResponse($model);
    }



    private function normalizeUser($user, array $overrides = null) {
        $user = $this->filterModel($user, $overrides);

        return $user;
    }



    // public function sample(Request $req, Response $res) : Response {

    //     $data = Validator::validate($body, [
    //         [ 'email',      'required|email',   FILTER_SANITIZE_EMAIL           ],
    //         [ 'password',   'required|min:9',   FILTER_SANITIZE_SPECIAL_CHARS   ],
    //     ]);

    //     // if Validator returned a 'messages' field, then we got problems with the data
    //     if (isset($data['messages'])) {
    //         return errorResponse($data['messages'], HTTP_BAD_REQUEST);
    //     }

    //     $user = R::findOne('user', 'email = ?', [ $data['email'] ]);


    //     //
    //     // HANDLE ERROR STATES
    //     //

    //     if (isEmpty($user)) {
    //         return errorResponse('User could not be found with credentials provided', HTTP_BAD_REQUEST);
    //     }

    //     if (!Hash::verify($data['password'], $user->password)) {
    //         return errorResponse('User could not be found with credentials provided', HTTP_BAD_REQUEST);
    //     }


    //     //
    //     // INIT SESSION VALUES
    //     //

    //     Session::set('user_id', $user->uuid);
    //     Session::set('is_admin', !!$user->is_admin);


    //     //
    //     // SETUP RESPONSE MODEL
    //     //

    //     $model = [];

    //     $model['user'] = filterJson($user->export(), [
    //         'id'        => 'int',
    //         'email'     => 'email',
    //         'username'  => 'string',
    //         'is_admin'  => 'bool',
    //     ]);

    //     return jsonResponse($model);
    // }



}
