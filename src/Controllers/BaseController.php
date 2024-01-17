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



class BaseController {

    protected $table = null;


    protected $modelSchema = [
        'id'            => 'int',
        'created_at'    => 'date',
        'deleted_at'    => 'date',
    ];


    //
    // CRUD METHODS
    //

    // protected function create(string $where = '', array $values = [], callable $cb) {
    //     $model = R::dispense($this->table);

    //     $model = cb($model);

    //     return R::store($model);
    // }


    protected function readAll(string $where = '', array $values = []) {
        return R::findAll($this->table, $where, $values);
    }


    protected function read(string $where = '', array $values = []) {
        return R::read($this->table, $where, $values);
    }


    protected function update() {

    }


    protected function delete() {

    }


    //
    // HELPER METHODS
    //


    protected function isUser(string $user_id) {
        return Session::get('user_id') === $user_id;
    }


    protected function isAdmin() {
        $user = R::findOne('user', 'uuid = :uuid', [ 'uuid' => Session::get('user_id') ]);
        return str_contains($user->roles, 'admin');
    }



    protected function filterModel($model, array $modelSchema = null) {
        $modelSchema = $modelSchema ?? $this->modelSchema;
        return filterJson($model, $modelSchema);
    }


    protected function paginate(int $pageIndex, int $pageSize, int $count) {

        $pageCount = ceil($count / $pageSize);

        $currentPage = $pageIndex + 1;

        return [
            'page'  => $currentPage,
            'pages' => $pageCount,
            'prev'  => $currentPage - 1 < 1 ? null : $currentPage - 1,
            'next'  => $currentPage + 1 < $pageCount ? $currentPage + 1 : null,
        ];
    }

}


