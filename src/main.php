<?php

use App\Controllers;
use App\Helpers\Hash;
use App\Helpers\Template;
use App\Helpers\Validator;
use App\Middleware;
use App\Models\Session;


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

$app->addErrorMiddleware(
    $displayErrorDetails    = true,
    $logErrors              = true,
    $logErrorDetails        = true
);


if (getenv('PHP_ENV') === 'production') {
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



$app->group('/api', function(RouteCollectorProxy $api) {

    $api->post('/auth/login',   [ Controllers\AuthController::class, 'login' ])
        ;

    $api->post('/auth/logout',  [ Controllers\AuthController::class, 'logout' ])
        ->add(Middleware\VerifyIpAddress::class)
        ;

    $api->group('', function(RouteCollectorProxy $client) {

        $client->get('/users[/{index}]', [ Controllers\UserController::class, 'getUsers' ])
            ->add(Middleware\UserIsAdmin::class)
            ;

    })
        ->add(Middleware\UserIsLoggedIn::class)
        ;

});




require __DIR__ . '/routes/AppController.php';


// START APP
$app->run();


// CLEANUP
R::close();
