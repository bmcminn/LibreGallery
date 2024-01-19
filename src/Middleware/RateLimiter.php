<?php
namespace App\Middleware;

use App\Helpers\Config;
use App\Helpers\Logger;
use App\Models\Session;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

use RedBeanPHP\Facade as R;


//
// POSSIBLE OPTIONS:
// - https://packagist.org/packages/davedevelopment/stiphle
// - https://packagist.org/packages/nikolaposa/rate-limit
// - https://packagist.org/packages/symfony/rate-limiter


class RateLimiter {

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
