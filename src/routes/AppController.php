<?php

use App\Template;
use App\Middleware;
use App\Helpers\Config;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response7;


class AppController {


    public function app (Request $req, Response $res) {
        $body = 'home';

        $filepath = path('/public/index.html');
        $body = file_get_contents($filepath);

        $config = [
            'routes' => Config::get('public_routes'),
        ];

        // Pass token verification to client AppConfig
        $hasToken = $req->getAttribute('hasToken');

        if ($hasToken) {
            $config['isValidToken'] = $req->getAttribute('isValidToken');
        }

        $config = json_encode($config);

        $src = <<<SRC
            <script>
                window.AppConfig = $config
            </script>
        SRC;

        $src    = preg_replace('/\s+/i', '', $src);

        $body   = preg_replace('/<!--\s*PHPINCLUDE\s*-->/i', trim($src), $body);

        $res->getBody()->write($body);

        return $res;
    }


}


$app->get('/{path:.*}', AppController::class . ':app')
    ->add(\App\Middleware\ValidatePasswordResetToken::class)
;
