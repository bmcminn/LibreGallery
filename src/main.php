<?php

use App\Helpers\Hash;
use App\Helpers\Template;
use App\Helpers\Validator;
use App\Models\Session;
use App\Middleware\JsonBodyParserMiddleware;
use RedBeanPHP\Facade as R;
use Slim\Factory\AppFactory;

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



use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;
use Slim\Routing\RouteCollectorProxy;

use App\Middleware;


//
// ROUTE DEFINTIONS
//



$app->group('/api', function(RouteCollectorProxy $api) {


    $api->post('/auth/login', function(Request $req, Response $res) {
        $body = $req->getParsedBody();

        $data = Validator::validate($body, [
            [ 'email',      'required|email',   FILTER_SANITIZE_EMAIL           ],
            [ 'password',   'required|min:9',   FILTER_SANITIZE_SPECIAL_CHARS   ],
        ]);

        // if Validator returned a 'messages' field, then we got problems with the data
        if (isset($data['messages'])) {
            return errorResponse($data['messages'], HTTP_BAD_REQUEST);
        }

        $user = R::findOne('user', 'email = ?', [ $data['email'] ]);


        //
        // HANDLE ERROR STATES
        //

        if (isEmpty($user)) {
            return errorResponse('User could not be found with credentials provided', HTTP_BAD_REQUEST);
        }

        if (!Hash::verify($data['password'], $user->password)) {
            return errorResponse('User could not be found with credentials provided', HTTP_BAD_REQUEST);
        }


        //
        // INIT SESSION VALUES
        //

        Session::set('user_id', $user->uuid);
        Session::set('is_admin', !!$user->is_admin);
        Session::set('ipaddress', $_SERVER['REMOTE_ADDR']);


        //
        // SETUP RESPONSE MODEL
        //

        $model = [];

        $model['user'] = filterJson($user->export(), [
            'id'    => 'int',
            'email' => 'email',
        ]);

        return jsonResponse($model);
    });



    $api->post('/auth/logout', function(Request $req, Response $res) {

        // TODO: convert this to Middleware\VerifyIpAddress
        if (S_SERVER['REMOTE_ADDR'] !== Session::get('ipaddress')) {
            // TODO: add log entry of attempted logout of mismatching IP ADDRESSES
            return errorResponse('User cannot perform that action at this time');
        }

        Session::destroy();

        $data = [];
        return jsonResponse($data, 200);
    });



    $api->group('', function(RouteCollectorProxy $group) {

        $group->get('/users', function(Request $req, Response $res) {

            $users = R::findAll('user');

            $model['users'] = R::exportAll($users);

            foreach ($model['users'] as $i => $user) {
                $model['users'][$i] = filterJson($user, [
                    'id'        => 'int',
                    'uuid'      => 'string',
                    'email'     => 'email',
                    'is_admin'  => 'bool',
                ]);
            }

            return jsonResponse($model);
        })
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
