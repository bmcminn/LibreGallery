<?php

namespace App\Controllers;

use App\Helpers\Hash;
use App\Helpers\Template;
use App\Helpers\Validator;
use App\Models\Session;
use App\Models\User;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;
use Slim\Routing\RouteCollectorProxy;
use RedBeanPHP\Facade as R;



class UserController extends BaseController {

    protected $table = 'user';

    protected $pageLimit = 10;
    protected $pageLimitMax = 100;

    protected $modelSchema = [
        'id'            => 'int',
        'uuid'          => 'string',
        'username'      => 'string',
        'email'         => 'email',
        'is_admin'      => 'bool',
        'roles'         => 'string',
        'created_at'    => 'datetime',
        'deleted_at'    => 'datetime',
        'last_login'    => 'datetime',
    ];



    /**
     * Gets all users.
     *
     * @param      Request   $req     The request
     * @param      Response  $res     The resource
     * @param      array     $params  The parameters
     *
     * @return     <type>    The users.
     */
    public function index(Request $req, Response $res, array $params) {
        // $body = $req->getParsedBody();
        if (!$this->isAdmin()) {
            return errorResponse('action is not permitted', HTTP_UNAUTHORIZED);
        }

        $userCount = UserModel::count(); // R::count($this->table);

        $limit  = max($params['limit']) ?? $this->pageLimit;

        $index  = clamp($params['index'] ?? 0, 0, ceil($userCount / $limit));

        $offset = $index * $limit;

        $users  = UserModel::index($limit, $offset);

        $model  = [];
        $model['pagination']    = $this->paginate($index, $limit, $userCount);
        $model['users']         = R::exportAll($users);

        foreach ($model['users'] as $i => $user) {
            $model['users'][$i] = $this->normalizeUser($user);
        }

        return jsonResponse($model);
    }


    public function create(Request $req, Response $res, array $params) {

    }


    /**
     * Reads an user.
     *
     * @param      Request   $req     The request
     * @param      Response  $res     The resource
     * @param      array     $params  The parameters
     *
     * @return     <type>    ( description_of_the_return_value )
     */
    public function read(Request $req, Response $res, array $params) {

        $id = $params['id'] ?? null;

        if (!$id) { return errorResponse('request missing required "id" parameter', HTTP_BAD_REQUEST); }

        $user = R::findOne($this->table, 'uuid = :uuid', [ 'uuid' => $id ]);

        $model = [];
        $model['user'] = $user->export();

        return jsonResponse($model);
    }


    /**
     * [update description]
     * @param  Request  $req    [description]
     * @param  Response $res    [description]
     * @param  array    $params [description]
     * @return [type]           [description]
     */
    public function update(Request $req, Response $res, array $params) {

        $id = $params['id'] ?? null;

        if (!$this->isUser($id) || !$this->isAdmin()) {
            return errorResponse('action is not permitted', HTTP_UNAUTHORIZED);
        }

        $user = R::findOne($this->table, 'uuid = :uuid', [ 'uuid' => $id ]);

        $body = $req->getParsedBody();

        $model = [];

        $model['user'] = $user;

        return jsonResponse($model);
    }



    //
    // HELPERS
    //

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
