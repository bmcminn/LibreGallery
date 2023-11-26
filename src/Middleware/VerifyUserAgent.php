<?php
namespace App\Middleware;

use App\Helpers\Config;
use App\Helpers\Logger;
use App\Models\Session;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

use RedBeanPHP\Facade as R;


class VerifyUserAgent {

    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $req PSR-7 request
     * @param  RequestHandler $next PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $req, RequestHandler $next) : Response {

        if (getUserAgent() !== Session::get('useragent')) {
            return errorResponse('User cannot perform that action at this time');
        }

        $res = $next->handle($req);

        return $res;
    }
}
