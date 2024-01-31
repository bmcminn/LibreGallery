<?php

use App\Helpers\Config;
use App\Helpers\Hash;
use App\Helpers\Logger;
use App\Helpers\Template;
use App\Helpers\Token;
use App\Helpers\Validator;
use App\Models\Session;

use RedBeanPHP\Facade as R;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();


define('HTTP_SUCCESS', 200);
define('HTTP_CREATED', 201);

define('HTTP_MOVED_PERMANENTLY', 301);
define('HTTP_MOVED_TEMPORARILY', 302);
define('HTTP_PERMANENT_REDIRECT', 302);

define('HTTP_BAD_REQUEST', 400);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_PAYMENT_REQUIRED', 402);
define('HTTP_FORBIDDEN', 403);
define('HTTP_NOT_FOUND', 404);
define('HTTP_RATE_LIMITED', 429);

define('HTTP_SERVER_ERROR', 500);


require __DIR__ . '/appconfig.php';



/**
 * ============================================================
 *  DOCUMENT VARIOUS FOLDER LOCATIONS
 * ============================================================
 */

// TODO: adjust folder permissions and can test whether it works or not
$dirs = [
    [ Config::get('paths.cache_dir'),       0766 ],
    [ Config::get('paths.database_dir'),    0766 ],
    [ Config::get('paths.logs_dir'),        0766 ],
    [ Config::get('paths.sessions_dir'),    0766 ],
    [ Config::get('paths.views_dir'),       0766 ],
];

if (is_dev()) {
    array_push($dirs, [ Config::get('paths.migrations_dir'),  0766 ]);
}

mkdirs($dirs);



/**
 * ============================================================
 *  HASHING UTILITY
 * ============================================================
 */

Hash::setup();



/**
 * ============================================================
 *  LOGGER UTILITY
 * ============================================================
 */

Logger::setup([
    'logs_path' => Config::get('paths.logs_dir'),
    'max_logs'  => 20,
]);



/**
 * ============================================================
 *  SESSION UTILITY
 * ============================================================
 */

Session::setup([
    'path'      => Config::get('paths.sessions_dir'),
    // 'expires'   => Config::get('session.expires'),
]);

// Capture the origin IP address so we can compare as needed later
Session::set('ipaddress', $_SERVER['REMOTE_ADDR'] ?? null);



/**
 * ============================================================
 *  TOKEN UTILITY
 * ============================================================
 */

Token::setup();



/**
 * ============================================================
 *  TEMPLATE UTILITY
 * ============================================================
 */


/**
 * [templateUrl description]
 * @param  string $pathname [description]
 * @return [type]           [description]
 */
function templateUrl(string $pathname) {
    return $pathname;
}

Template::setup([
    'model'     => Config::get(),
    'ext'       => '.twig',
    'cache_dir' => path(Config::get('paths.cache_dir') . '/views'),
    'views_dir' => Config::get('paths.views_dir'),
    'filters' => [
        'url' => 'templateUrl'
    ],
]);



/**
 * ============================================================
 *  DATABASE  UTILITY
 * ============================================================
 */

R::setup('sqlite:' . Config::get('paths.database_file'));
R::useFeatureSet( 'novice/latest' );

if (is_dev()) {
    $mlPath = path(Config::get('paths.migrations_dir', 'migration_' . date('Y-m-d') . '.sql'));
    $ml = new MigrationLogger($mlPath);

    R::getDatabaseAdapter()
        ->getDatabase()
        ->setLogger($ml)
        ->setEnableLogging(true)
        ;
}
