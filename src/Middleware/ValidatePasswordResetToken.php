<?php
namespace App\Middleware;

use App\Helpers\Config;
use App\Helpers\Logger;
use App\Helpers\Token;
use App\Models\Session;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

use RedBeanPHP\Facade as R;


class ValidatePasswordResetToken {

    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $req PSR-7 request
     * @param  RequestHandler $next PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $req, RequestHandler $next) : Response {

        $query = $req->getQueryParams();

        $token = $query['token'] ?? false;
        $route = $_SERVER['REQUEST_URI'];

        $isPasswordResetRoute = str_contains($route, '/password-reset');


        if ($isPasswordResetRoute && !!$token) {

            $bean = R::findOne(
                'token',
                'type = :type AND token = :token',
                [
                    'type' => Token::TYPE_PASSWORD_RESET,
                    'token' => $token,
                ]
            );

            $req = $req->withAttribute('hasToken', true);
            $req = $req->withAttribute('isValidToken', !!$bean);
        }


        $res = $next->handle($req);

        return $res;
    }
}
