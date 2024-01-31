<?php

use App\Classes\Console;
use App\Helpers\Token;
use App\Logger;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UploadedFileInterface;
use Rakit\Validation\Validator;
use RedBeanPHP\Facade as R;
use Ramsey\Uuid\Uuid;
// use Pecee\SimpleRouter\SimpleRouter as Router;
// use Pecee\Http\Url;
// use Pecee\Http\Response;
// use Pecee\Http\Request;


if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }


if (!function_exists('is_dev')) {
    function is_dev() {
        return !is_prod();
    }
}

if (!function_exists('is_prod')) {
    function is_prod() {
        return env('APP_ENV', 'prod') === 'prod';
    }
}



/**
 * Determines whether the specified value is empty.
 *
 * @param      <type>  $value  The value
 *
 * @return     bool    True if the specified value is empty, False otherwise.
 */
function isEmpty($value) {
    return !!!$value;
    // if ($)
    // $isArray = is_array($value) && count($value) > 0;
    // $isString = is_string($value) && strlen($value) > 0;
    // if ($isArray || $isString) {
    //     return false;
    // }
    // return true;

}


if (!function_exists('env')) {
    /**
     * { function_description }
     *
     * @param      string       $key      The key
     * @param      <type>       $default  The default
     *
     * @return     bool|string  ( description_of_the_return_value )
     */
    function env(string $key, $default = null) {
        $value = $_ENV[$key] ?? null;

        // $value = getenv($key);
        // throw_when(!$key and is_null($default), "{$key} is not a defined .env variable and has no default value." );
        // $value = getenv($key);
        // throw_when(!$key and is_null($default), "{$key} is not a defined .env variable and has no default value." );


        if (!$value && $default) { return $default; }
        if ($value === 'true') { return true;}
        if ($value === 'false') { return false; }

        return $value;
    }
}



function env_bool(string $key, bool $default = null) {

}



/**
 * Use a path query string to traverse an associative array for a given value
 * @param      string           $keyPath  The key path
 * @param      array            $data     The data
 * @throws     \ErrorException  Error if key does not exist in array structure
 * @return     any              Whatever data exists in the given $keyPath location
 */
function array_query(string $keyPath, array $data) {
    // TODO: memcache this thing
    // // setup memoization to trivialize future lookups
    // static $memocache = [];
    // if (isset($memocache[$key])) {
    //     return $memocache[$key];
    // }
    $parts = explode('.', $keyPath);
    $value = $data;
    foreach ($parts as $part) {
        if (!isset($value[$part])) {
            throw new ErrorException("key path does not exist ($keyPath)");
        }
        $value = $value[$part];
    }
    // $memocache[$key] = $value;
    return $value;
}



function dateFromTimestamp(int $timestamp): \DateTime {
    return (new DateTime())->setTimestamp($timestamp);
}


/**
 * [now description]
 * @param  int|integer $offset [description]
 * @return [type]              [description]
 */
function now(int $offset = 0): int {
    return time() + $offset;
}


/**
 * [millis description]
 * @param  [type] $seconds [description]
 * @return [type]          [description]
 */
function millis($seconds): int {
    return (int) floor($seconds *= 1000);
}


/**
 * [seconds description]
 * @param  int|integer $n [description]
 * @return [type]         [description]
 */
function seconds(int $n = 1): int {
    return $n;
}


/**
 * [minutes description]
 * @param  int|integer $n [description]
 * @return [type]         [description]
 */
function minutes(int $n = 1): int {
    return seconds(60) * $n;
}


/**
 * [hours description]
 * @param  int|integer $n [description]
 * @return [type]         [description]
 */
function hours(int $n = 1): int {
    return minutes(60) * $n;
}


/**
 * [days description]
 * @param  int|integer $n [description]
 * @return [type]         [description]
 */
function days(int $n = 1): int {
    return hours(24) * $n;
}


/**
 * [path description]
 * @param  string $path [description]
 * @return [type]       [description]
 */
function path(string $path): string {
    return getcwd() . '/../' . trim($path, '/');
}


/**
 * [makeUrl description]
 * @param  string $path  [description]
 * @param  array  $query [description]
 * @return [type]        [description]
 */
function makeUrl(string $path, array $query=[]): string {
    // $host = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_HOST'];
    $scheme = $_SERVER['REQUEST_SCHEME'] ?? 'http';
    $scheme .= '://';
    $host = $_SERVER['SERVER_NAME'] ?? 'localhost';
    $port = $_SERVER['SERVER_PORT'] ?? '80';
    $port = $port !== '80' ? ":{$port}" : '';
    if (!empty($query)) {
        $query = '?' . http_build_query($query);
    } else {
        $query = '';
    }

    return implode([$scheme, $host, $port, $path, $query]);
}



// /**
//  * Get url for a route by using either name/alias, class or method name.
//  * The name parameter supports the following values:
//  * - Route name
//  * - Controller/resource name (with or without method)
//  * - Controller class namdashboardcollections * When searching for controller/resource by name, you can use this syntax "route.name@method".
//  * You can also use the same syntax when searching for a specific controller-class "MyController@home".
//  * If no arguments is specified, it will return the url for the current loaded route.
//  * @param string|null $name
//  * @param string|array|null $parameters
//  * @param array|null $getParams
//  * @return \Pecee\Http\Url
//  * @throws \InvalidArgumentException
//  */
// function getUrl(?string $name = null, $parameters = null, ?array $getParams = null) :Url {
//     return Router::getUrl($name, $parameters, $getParams);
// }


/**
 * [mkdirs description]
 * @param  array  $paths [description]
 * @return [type]        [description]
 */
function mkdirs(array $paths) {
    // $paths = [
    //     [ "/dir/path/here",  0766 ],
    //     ...
    // ]

    foreach ($paths as $path) {
        [ $filepath, $permissions ] = $path;
        if (is_dir($filepath)) { continue; }
        mkdir($filepath, $permissions, $recursive = true);
    }
}


/**
 * [filterJson description]
 * @param  [type] $fields      [description]
 * @param  array  $allowedList [description]
 * @return [type]              [description]
 */
function filterJson($fields, array $allowedList) {
    $res    = [];
    foreach ($allowedList as $key => $filter) {
        $value  = $fields[$key] ?? null;

        $filter = trim(strtolower($filter));

        switch($filter) {
            case 'bool':
            case 'boolean':
                $value = (bool) $value;
                break;
            case 'float':
                break;
                $value = (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
            case 'int':
                break;
                $value = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            case 'email':
                break;
                $value = filter_var($value, FILTER_SANITIZE_EMAIL);
            case 'string':
                break;
                $value = (string) $value;
            case 'array':
                break;
                $value = (array) $value;
            case 'date':
                break;
                $value = date($filter);
            case 'datetime':
                $value = $value
                    ? (new DateTime($value))->format('Y-m-d H:i:s')
                    : null;
                break;
        }
        $res[$key] = $value;
    }
    return $res;
}


/**
 * [getUserAgent description]
 * @return [type] [description]
 */
function getUserAgent() {
    return implode('.', [
        $_SERVER['HTTP_USER_AGENT'] ?? '',
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
        $_SERVER['HTTP_SEC_CH_UA'] ?? '',
    ]);
}


/**
 * [minmax description]
 * @param  float         $value [description]
 * @param  float|integer $min   [description]
 * @param  float|null    $max   [description]
 * @return [type]               [description]
 */
function minmax(int|float $value, int|float $min = 0, int|float $max = null) {
    if ($value < $min) { return $min; }
    if ($max && $value > $max) { return $max; }
    return $value;
}



/**
 * Enter any number of comma separated arguments to compose a debug message to the PHP console
 */
function dbg() {
    $args = func_get_args();
    $msg = [];
    foreach ($args as $arg) {
        $prefix = '';
        switch(gettype($arg)) {
            case 'object':
            case 'array':
                $prefix = '[' . gettype($arg) . ']';
                $arg = json_encode($arg, JSON_PRETTY_PRINT);
                break;
            case 'boolean':
            case 'bool':
                $arg = $arg === true ? 'TRUE' : 'FALSE';
        }

        array_push($msg, $prefix . $arg);
    }

    file_put_contents('php://stdout', implode("\n", $msg) . "\n");
}



/**
 * { function_description }
 * @param      int    $value  The value
 * @param      int    $a      { parameter_description }
 * @param      int    $b      { parameter_description }
 * @throws     Error  (description)
 * @return     int    ( description_of_the_return_value )
 */
function clamp( $value, $a, $b) {
    if ($b < $a) {
        throw new Error("clamp(\$a = $a, \$b = $b): \$b cannot be less than \$a.");
    }

    if ($value < $a) { return $a; }
    if ($value > $b) { return $b; }
    return $value;
}




// =============================

function toMicrotime(int $time) :int {
    return $time * 1000;
}



function fromMicrotime(int $timestamp) :int {
    return floor($time / 1000);
}

// ===========================================================================
// ===========================================================================
// ===========================================================================
// ===========================================================================




/**
 * Get url for a route by using either name/alias, class or method name.
 * The name parameter supports the following values:
 * - Route name
 * - Controller/resource name (with or without method)
 * - Controller class name
 * When searching for controller/resource by name, you can use this syntax "route.name@method".
 * You can also use the same syntax when searching for a specific controller-class "MyController@home".
 * If no arguments is specified, it will return the url for the current loaded route.
 * @param string|null $name
 * @param string|array|null $parameters
 * @param array|null $getParams
 * @return \Pecee\Http\Url
 * @throws \InvalidArgumentException
 */
// function url(?string $name = null, $parameters = null, ?array $getParams = null) :Url {
//     return Router::getUrl($name, $parameters, $getParams);
// }

// /**
//  * @return \Pecee\Http\Response
//  */
// function response() :Response {
//     return Router::response();
// }



// /**
//  * @return \Pecee\Http\Request
//  */
// function request() :Request {
//     return Router::request();
// }



// /**
//  * Get input class
//  * @param string|null $index Parameter index name
//  * @param string|null $defaultValue Default return value
//  * @param array ...$methods Default methods
//  * @return \Pecee\Http\Input\InputHandler|array|string|null
//  */
// function input($index = null, $defaultValue = null, ...$methods) {
//     if ($index !== null) {
//         return request()->getInputHandler()->value($index, $defaultValue, ...$methods);
//     }

//     return request()->getInputHandler();
// }



// /**
//  * @param string $url
//  * @param int|null $code
//  */
// function redirect(string $url, ?int $code = null) :void {
//     if ($code !== null) {
//         response()->httpCode($code);
//     }

//     response()->redirect($url);
// }



// /**
//  * Get current csrf-token
//  * @return string|null
//  */
// function csrf_token() :?string {
//     $baseVerifier = Router::router()->getCsrfVerifier();
//     if ($baseVerifier !== null) {
//         return $baseVerifier->getTokenProvider()->getToken();
//     }

//     return null;
// }

function base_path(string $path='') { return __DIR__ . "/../../{$path}"; }
function storage_path(string $path='') { return base_path("storage/{$path}"); }
function resource_path(string $path='') { return base_path("resources/{$path}"); }
function src_path(string $path='') { return base_path("src/{$path}"); }

if (!function_exists('throw_when')) {
    function throw_when(bool $condition, string $message, string $exception = Exception::class) {
        if (!$condition) { return; }

        throw new $exception($message);
    }
}

/**
 * Valdation macro for Rakit Validator
 * @sauce  https://github.com/rakit/validation
 * @param  array    $data   Associative array of values to validate
 * @param  array    $config Collection of parameters and their validation controls per the Rakit Validation documentation
 * @return array            If errors, returns list of errors; Else empty array
 */
function validate(array $data, array $rules) {
    $validator = new \Rakit\Validation\Validator;
    // $validation = $validator->validate($_POST + $_FILES, [
    $validation = $validator->validate($data, $rules);
    if ($validation->fails()) {
        return $validation->errors();
    }

    return [];
}




// /**
//  * { function_description }
//  * @sauce  https://github.com/rakit/validation
//  * @param      Response               $res       The resource
//  * @param      array|object           $data      The data
//  * @param      array                  $rules     The rules
//  * @param      array                  $messages  The messages
//  * @return     Response|array|object  ( description_of_the_return_value )
//  */
// function validate(Response $res, object|array $data, array $rules, array $messages = []) {

//     $data = (array) $data;

//     // filter $data down exclusively to the rules
//     $data = array_filter($data, function($key) use ($rules) {
//         return isset($rules[$key]);
//     }, ARRAY_FILTER_USE_KEY);

//     // EXTRACT VALIDATIONS
//     $validations = array_map(function($rule) {
//         return $rule[0];
//     }, $rules);

//     // VALIDATE DATA BASED ON RULES SCHEMA
//     $validator = new Validator();
//     $validation = $validator->validate($data, $validations, $messages);
//     if ($validation->fails()) {
//         $errors = $validation->errors();
//         json($res, $errors->firstOfAll(), 500);
//         return $res;
//         exit;
//     }

//     // SANITIZE DATA AS NEEDED
//     foreach ($data as $key => $value) {
//         $filter = $rules[$key][1] ?? null;
//         if (!$filter || gettype($filter) === 'string') {
//             $value = $value === null
//                 ? htmlspecialchars((string) $value)
//                 : call_user_func($filter, $value)
//                 ;
//         } else {
//             $value = filter_var($value, $filter);
//         }

//         // if (gettype($filter) === 'string') {
//         //     if ($value === null) {
//         //         continue;
//         //     }
//         //     $value = call_user_func($filter, $value);
//         // } else {
//         //     $value = filter_var($value, $filter);
//         // }

//         $data[$key] = $value;
//     }

//     return $data;
// }





/**
 * Filters an associative array to include only the data you want
 * @param  array    $data   Associative array of data to filter
 * @param  array    $list   List of properties to be captured
 * @return array            Filtered list of desired properties
 */
function array_allow_keys(array $data, array $keys) :array {
    $res = [];

    foreach ($keys as $i => $key) {
        if ($data[$key]) {
            $res[$key] = $data[$key];
        }
    }

    return $res;
}



/**
 * Strips restricted keys from a given associative Array
 * @note: not deep search compatible... yet
 *
 * @param      array  $data            The data
 * @param      array  $restrictedKeys  The restricted keys
 *
 * @return     array  The filtered array less restricted keys
 */
function array_disallow_keys(array $data, array $restrictedKeys=[]) :array {
    foreach ($data as $key => $value) {
        if (in_array($key, $restrictedKeys)) {
            unset($data[$key]);
        }
    }

    return $data;
}



/**
 * Array helper to determine if a given collection has some required values
 * @param   array   $data   Data to be checked
 * @param   array   $list   List of required properties to be checked
 * @return  bool            True if passes, false if failed
 */
function has(array $data, array $list=[]) :bool {
    foreach ($list as $i => $key) {
        if (!$data[$key]) {
            throw new \Exception("Missing property: Data must have property {$key}");
            return false;
        }
    }

    return true;
}



/**
 * Alias for password_hash that provides PASSWORD_ARGON2I by default
 * @param  string $pass Password to be hashed
 * @return string       Hashed password
 */
function hash_password(string $pass) :string {
    return password_hash($pass, PASSWORD_ARGON2I);
}



/**
 * Alias for password_needs_rehash that provides PASSWORD_ARGON2I by default
 * @param  string $hash Password to be hashed
 * @return string       Hashed password
 */
function rehash_password(string $hash) :string {
    return password_needs_rehash($hash, PASSWORD_ARGON2I);
}



/**
 * Iterates over a given directory and requires all files within
 * @param  string $dir [description]
 * @return [type]      [description]
 */
function require_dir(string $dir) {
    $models = scandir($dir);
    foreach ($models as $filepath) {
        if ($filepath === '.' || $filepath === '..' || strpos($filepath, '.php') === false) { continue; }
        $filepath = $dir. '/' . $filepath;
        if (file_exists($filepath)) {
            require_once($filepath);
        }
    }
}



/**
 * Initializes a json response
 * @param      Response      $res    The resource
 * @param      array|object  $data   The data
 * @param      int           $code   The code
 */
function json(Response &$res, array|object $data, int $code = 200): void {
    $res->getBody()
        ->write(json_encode($data))
        ;
    $res
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($code)
        ;
}



/**
 * [moveUploadedFile description]
 * @param  string                   $directory      [description]
 * @param  UploadedFileInterface    $uploadedFile   [description]
 * @param  int                      $permissions    [description]
 * @return string                                   The resulting filepath of the uploaded document
 */
function moveUploadedFile(string $directory, UploadedFileInterface $uploadedFile, $permissions=0766): string {
    // make directory if it doesn't exist already
    if (!is_dir($directory)) {
        mkdir($directory, $permissions, true);
    }

    $extension      = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    // see http://php.net/manual/en/function.random-bytes.php
    $basename       = Token::generateUuid(); // Uuid::uuid4();
    $filename       = sprintf('%s.%0.8s', $basename, $extension);
    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
    return $filename;
}



/**
 * [uploadsFile description]
 * @param  [type] $filename [description]
 * @return [type]           [description]
 */
function uploadsFile($filename) {
    return "/uploads/${filename}";
}



/**
 * Abstracting function for generating UUID strings
 * @return [type] [description]
 */
function uuid(int $type = 4) {
    $res = null;

    switch($type) {
        case 0: Uuid::uuid(); break;
        case 1: Uuid::uuid1(); break;
        case 2: Uuid::uuid2(); break;
        case 3: Uuid::uuid3(); break;
        case 4: Uuid::uuid4(); break;
        case 5: Uuid::uuid5(); break;
        case 6: Uuid::uuid6(); break;
        case 7: Uuid::uuid7(); break;
        case 8: Uuid::uuid8(); break;
    }

    return $res->toString();
}



/**
 * [modelCreate description]
 * @param  string $type [description]
 * @param  array  $data [description]
 * @return [type]       [description]
 */
function modelCreate(string $type, array $data) {
    $model = R::dispense($type);

    foreach ($data as $key => $value) {
        $model[$key] = $value;
    }

    $model->uuid        = uuid(); // Uuid::uuid4()->toString();
    $model->createdAt   = new DateTime();
    $model->updatedAt   = null;
    $model->deletedAt   = null;
    $id = R::store($model);
    // TODO: export model as array...
    $model = R::load($type, $id);
    return $model->export();
}



/**
 * [modelQuery description]
 * @param  string $type   [description]
 * @param  string $query  [description]
 * @param  array  $values [description]
 * @return [type]         [description]
 */
function modelQuery(string $type, string $query, array $values) {
    $result = R::find($type, $query, $values);
    return R::exportAll($result);
}



/**
 * [modelPaginate description]
 * @param  string      $type  [description]
 * @param  int|integer $limit [description]
 * @param  int|integer $page  [description]
 * @param  string      $query [description]
 * @return [type]             [description]
 */
function modelPaginate(string $type, int $limit = 10, int $page = 1, string $query = '') {
    $count  = R::count($type);
    $limit  = max(1, abs($limit));          // ensure value is min +1
    $page   = max(1, abs($page));           // ensure value is min +1

    $first  = 1;
    $last   = (int) ceil($count / $limit);  // type cast to ensure consistency
    $last   = max(1, $last);                // ensure value is min +1

    $page   = min($page, $last);            // clamp page from 1 to $last

    $prev   = max($first, $page - 1);       // clamp $prev
    $next   = min($last, $page + 1);        // clamp $prev

    if ($page === $first) { $first = null; $prev = null; }
    if ($page === $last) { $last = null; $next = null; }

    $offset = ($page - 1) * $limit;
    $offset = $offset > $count ? $count : $offset;
    $results = [];
    $results = R::findAll($type, " ${query} LIMIT ${limit} OFFSET ${offset}" );
    Console::debug($type, $results);
    return [
        'entries'   => R::exportAll($results),
        'count'     => $count,
        'page'      => $page,
        'first'     => $first,
        'prev'      => $prev,
        'next'      => $next,
        'last'      => $last,
    ];
}



/**
 * [debugValue description]
 * @param  [type] $label [description]
 * @param  [type] $value [description]
 * @return [type]        [description]
 */
function debugValue($label, $value) {
    $res = [
        'name'      => $label,
        'type'      => gettype($value),
        'value'     => $value,
        'length'    => strlen((string) $value),
    ];
    print_r($res);
}



/**
 * [varName description]
 * @source https://stackoverflow.com/a/404637
 * @param  [type] $v [description]
 * @return [type]    [description]
 */
function varName( $v ) {
    $trace = debug_backtrace();
    $vLine = file( __FILE__ );
    $fLine = $vLine[ $trace[0]['line'] - 1 ];
    preg_match( "#\\$(\w+)#", $fLine, $match );
    print_r( $match );
}


// /**
//  * { function_description }
//  * @param      Response               $res       The resource
//  * @param      array|object           $data      The data
//  * @param      array                  $rules     The rules
//  * @param      array                  $messages  The messages
//  * @return     Response|array|object  ( description_of_the_return_value )
//  */
// function validate(Response $res, object|array $data, array $rules, array $messages = []) {

//     $data = (array) $data;
//     // filter $data down exclusively to the rules
//     $data = array_filter($data, function($key) use ($rules) {
//         return isset($rules[$key]);
//     }, ARRAY_FILTER_USE_KEY);
//     // EXTRACT VALIDATIONS
//     $validations = array_map(function($rule) {
//         return $rule[0];
//     }, $rules);
//     // VALIDATE DATA BASED ON RULES SCHEMA
//     $validator = new Validator();
//     $validation = $validator->validate($data, $validations, $messages);
//     if ($validation->fails()) {
//         $errors = $validation->errors();
//         json($res, $errors->firstOfAll(), 500);
//         return $res;
//         exit;
//     }

//     // SANITIZE DATA AS NEEDED
//     foreach ($data as $key => $value) {
//         $filter = $rules[$key][1] ?? null;
//         if (!$filter || gettype($filter) === 'string') {
//             $value = $value === null
//                 ? htmlspecialchars((string) $value)
//                 : call_user_func($filter, $value)
//                 ;
//         } else {
//             $value = filter_var($value, $filter);
//         }

//         // if (gettype($filter) === 'string') {
//         //     if ($value === null) {
//         //         continue;
//         //     }
//         //     $value = call_user_func($filter, $value);
//         // } else {
//         //     $value = filter_var($value, $filter);
//         // }

//         $data[$key] = $value;
//     }

//     return $data;
// }


/**
 * { function_description }
 * @param      string    $date    The date
 * @param      string    $format  The format
 * @return     DateTime  The date time.
 */
function dateToUTC(string $date, string $format='Y-m-d H:i:s'): DateTime {
    $date = preg_replace('/(\([\s\S]+?\))/', '', $date);
    $date = gmdate($format, strtotime($date));
    return new DateTime($date);
}



/**
 * [getImageURI description]
 * @param  [type] $path [description]
 * @return [type]       [description]
 */
function getImageURI($path) {
    return getRoute(false) . $path;
}

// /**
//  * Recomposes a URL based on a parse_url array result set
//  * @source https://www.php.net/manual/en/function.parse-url.php#106731
//  * @param  array  $parsed_url Parse URL data
//  * @return string             Unparsed URL
//  */
// function unparse_url(array $parsed_url): string {
//     $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
//     $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
//     $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
//     $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
//     $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
//     $pass     = ($user || $pass) ? "$pass@" : '';
//     $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
//     $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
//     $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
//     return "$scheme$user$pass$host$port$path$query$fragment";
// }


// /**
//  * Removes trailing slash from path and redirects accordingly
//  * @return [type] [description]
//  */
// function removeTrailingSlash() {
//     $route  = getRoute();

//     console($route);

//     if (strlen($route['path']) <= 1 || !str_ends_with($route['path'], '/')) {
//         return;
//     }

//     $route['path'] = substr_replace($route['path'], '', -1);
//     $url = unparse_url($route);

//     header("Location: " . $url);
//     exit;
// }



/**
 * { function_description }
 * @param      string  $str        The string
 * @param      string  $separator  The separator
 * @return     string  ( description_of_the_return_value )
 */
function slugify(string $str, string $separator = '-'): string {
    $str = trim($str);

    // remove leading/trailing spaces/hyphens and extraneous punctuation
    $str = preg_replace('/^[\s\-]*|[\s\-]*$|[^\w\d\-\s]*/gi/', '', $str);

    // replace all extraneous whitespace with hyphens
    $str = preg_replace('/[\s\-\_]*/', $separator, $str);

    return $str;
}



function registerCRUD(&$app, string $contoller, string $route) {
    $routeIndex = implode('/', array_slice(explode('/', $route), 0, 2));

    $app->post($route,      [ $controller, 'create' ]);
    $app->get($routeIndex,  [ $controller, 'index' ]);
    $app->get($route,       [ $controller, 'read' ]);
    $app->put($route,       [ $controller, 'update' ]);
    $app->delete($route,    [ $controller, 'delete' ]);
}
