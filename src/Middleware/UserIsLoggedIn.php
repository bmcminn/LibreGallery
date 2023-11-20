<?php
namespace App\Middleware;

use App\Helpers\Config;
use App\Helpers\Logger;
use App\Models\Session;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

use RedBeanPHP\Facade as R;


class UserIsLoggedIn {
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

        if (!$userId) {
            $model = [
                'message' => 'Request is not authorized to perform that action due to a missing or invalid user session.',
                'success' => false,
            ];

            return jsonResponse($model, HTTP_UNAUTHORIZED);
        }

        $res = $next->handle($req);

        // $res = new Response();

        return $res;
    }
}
