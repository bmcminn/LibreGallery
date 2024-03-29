<?php

use App\Helpers\Config;
use App\Controllers;
use App\Helpers\Hash;
use App\Helpers\Template;
use App\Helpers\Validator;
use App\Middleware;
use App\Models\Session;


use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use RedBeanPHP\Facade as R;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response as SlimResponse;
use Slim\Routing\RouteCollectorProxy;


require __DIR__ . '/../vendor/autoload.php';


// INITIALIZE SERVICES SETUP
Session::start();


// INIT APPLICATION
$app = AppFactory::create();


// SETUP MIDDLEWARE
$app->addRoutingMiddleware();

$app->addBodyParsingMiddleware();


// // This middleware will append the response header Access-Control-Allow-Methods with all allowed methods
// $app->add(function (Request $request, RequestHandler $handler): Response {
//     $routeContext = RouteContext::fromRequest($request);
//     $routingResults = $routeContext->getRoutingResults();
//     $methods = $routingResults->getAllowedMethods();
//     $requestHeaders = $request->getHeaderLine('Access-Control-Request-Headers');

//     $response = $handler->handle($request);

//     $response = $response->withHeader('Access-Control-Allow-Origin', '*');
//     $response = $response->withHeader('Access-Control-Allow-Methods', implode(',', $methods));
//     $response = $response->withHeader('Access-Control-Allow-Headers', $requestHeaders);
//     $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');

//     // TODO: enable authorization header for API queries
//     // Optional: Allow Ajax CORS requests with Authorization header
//     // $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');

//     return $response;
// });
//
//
$app->add(new \Tuupola\Middleware\CorsMiddleware, Config::get('cors'));

// /**
//  * Setup Lazy CORS handling
//  * https://www.slimframework.com/docs/v4/cookbook/enable-cors.html
//  */
// $app->add(function ($request, $handler) {
//     $response = $handler->handle($request);

//     $origins = Config::get('cors.origins');

//     // TODO: need a more robust way of allowing CORS handling
//     $http_origin = 'http://localhost:5173';

//     // $http_origin = in_array($origins, $_SERVER['HTTP_ORIGIN']) ? ;

//     // if (!in_array($http_origin, $origins)) {
//     //     return errorResponse('origin not allowed', HTTP_FORBIDDEN);
//     // }

//     return $response
//         ->withHeader('Access-Control-Allow-Origin',         $http_origin)
//         ->withHeader('Access-Control-Allow-Headers',        'X-Requested-With, Content-Type, Accept, Origin, Authorization')
//         ->withHeader('Access-Control-Allow-Methods',        'GET, POST, PUT, DELETE, PATCH, OPTIONS')
//         ->withHeader('Access-Control-Allow-Credentials',    'true');
//         ;
// });


$app->addErrorMiddleware(
    $displayErrorDetails    = true,
    $logErrors              = true,
    $logErrorDetails        = true
);


//
// CONTAINER STUFFS
//

$container = new Container();

AppFactory::setContainer($container);


// // Register Middleware On Container
// $container->set('csrf', function () use ($responseFactory) {
//     return new Guard($responseFactory);
// });


// Register the http cache middleware.
$app->add(new \Slim\HttpCache\Cache('public', 86400));

// Create the cache provider.
$container->set('httpcache', new \Slim\HttpCache\CacheProvider());


if (is_dev()) {
    // SETUP ROUTE CACHER
    $routeCollector = $app->getRouteCollector();
    $routeCollector->setCacheFile(__DIR__ . '/../storage/cache/routes.cache');

    // Freeze the database
    R::freeze(TRUE);
}


// // ADD ROUTES
// require __DIR__ . '/routes/AuthController.php';
// require __DIR__ . '/routes/UserController.php';
// require __DIR__ . '/routes/AppController.php';




//
// ROUTE DEFINTIONS
//


$app->options('/{routes:.+}', function (Request $req, Response $res) {
    return $res;
});



$app->group('/rpc', function(RouteCollectorProxy $rpc) {



});



$app->group('/api', function(RouteCollectorProxy $api) {


    //
    // AUTH ROUTE HANDLERS
    //

    $api->post('/auth/login',                   [ Controllers\AuthController::class, 'login' ]);
    $api->post('/auth/register',                [ Controllers\AuthController::class, 'register' ]);

    $api->post('/auth/logout',                  [ Controllers\AuthController::class, 'logout' ])
        ->add(Middleware\VerifyUserAgent::class)
        ;

    $api->post('/auth/password-reset',          [ Controllers\AuthController::class, 'passwordResetHandler' ])
        ;

    // $api->post('/auth/verify-password-reset',   [ Controllers\AuthController::class, 'verifyRegistration' ])
    $api->post('/auth/verify-password-reset',   [ Controllers\AuthController::class, 'verifyPasswordReset' ])
        ;


    /**
     * { item_description }
     */
    $api->group('', function(RouteCollectorProxy $client) {

        //
        // USER ROUTE HANDLERS
        //

        $client->post('/users/{uuid}',          [ Controllers\UserController::class, 'createUser' ]);
        $client->get('/users',                  [ Controllers\UserController::class, 'index' ]);
        $client->get('/users/{uuid}',           [ Controllers\UserController::class, 'readUser' ]);
        $client->put('/users/{uuid}',           [ Controllers\UserController::class, 'updateUser' ]);
        $client->delete('/users/{uuid}',        [ Controllers\UserController::class, 'deleteUser' ]);


        //
        // COLLECTIONS ROUTE HANDLERS
        //

        $client->post('/collections/{uuid}',    [ Controllers\UploadController::class, 'createCollection' ]);
        $client->get('/collections',            [ Controllers\UploadController::class, 'index' ]);
        $client->get('/collections/{uuid}',     [ Controllers\UploadController::class, 'readCollection' ]);
        $client->put('/collections/{uuid}',     [ Controllers\UploadController::class, 'updateCollection' ]);
        $client->delete('/collections/{uuid}',  [ Controllers\UploadController::class, 'deleteCollection' ]);


        //
        // UPLOAD ROUTE HANDLERS
        //



    })
        ->add(Middleware\UserIsLoggedIn::class)
        ;

});




require __DIR__ . '/routes/AppController.php';


// START APP
$app->run();


// CLEANUP
R::close();
