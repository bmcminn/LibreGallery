<?php
namespace App\Middleware;

use App\Helpers\Config;
use App\Helpers\Logger;
use App\Models\Session;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

use RedBeanPHP\Facade as R;


class UserIsAdmin {
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $req PSR-7 request
     * @param  RequestHandler $next PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $req, RequestHandler $next) : Response {
        $userId = Session::get('user_id');

        // Ensure that we are reading values from database, if is_admin is changed in database while user is logged in, $_SESSION will persist if/until it expires
        $user = R::findOne('user', 'uuid = ?', [ $userId ]);

        if (!!!$user->isAdmin) {
            $model = [
                'message' => 'User is not authorized to perform this action',
                'success' => false,
            ];

            return jsonResponse($model, HTTP_UNAUTHORIZED);
        }

        $res = $next->handle($req);

        // $res = new Response();

        return $res;
    }
}
