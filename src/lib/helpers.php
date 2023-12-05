<?php


if (!defined('DS')) { define('DS', DIRECTORY_SEPARATOR); }


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



function env($key, $default=null) {
    $value = $_ENV[$key] ?? null;
    // $value = getenv($key);

    if (!$value && $default) {
        return $default;
    }

    if ($value === 'true') {
        return true;
    }

    if ($value === 'false') {
        return false;
    }

    return $value;
}


/**
 * Use a path query string to traverse an associative array for a given value
 *
 * @param      string           $keyPath  The key path
 * @param      array            $data     The data
 *
 * @throws     \ErrorException  Error if key does not exist in array structure
 *
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


function now(int $offset = 0): int {
    return (int) (floor(microtime(true)) + $offset);
}

function millis($seconds): int {
    return (int) floor($seconds *= 1000);
}

function seconds(int $n = 1): int {
    return $n;
}

function minutes(int $n = 1): int {
    return seconds(60) * $n;
}

function hours(int $n = 1): int {
    return minutes(60) * $n;
}

function days(int $n = 1): int {
    return hours(24) * $n;
}


function path(string $path): string {
    return getcwd() . '/../' . trim($path, '/');
}


function url(string $path, array $query=[]): string {

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



function mkdirs(array $paths) {

    // $paths = [
    //     [ "/dir/path/here",  0766 ],
    //     [ "/dir/path/here",  0766 ],
    //     [ "/dir/path/here",  0766 ],
    //     ...
    // ]

    foreach ($paths as $path) {
        [ $filepath, $permissions ] = $path;
        if (is_dir($filepath)) { continue; }
        mkdir($filepath, $permissions, $recursive = true);
    }

}




function filterJson($fields, array $allowedList) {
    $res    = [];

    foreach ($allowedList as $key => $filter) {
        $value  = $fields[$key] ?? null;

        switch($filter) {
            case 'bool':
            case 'boolean':
                $value = (bool) $value;
                break;
            case 'float':
                $value = (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT);
                break;
            case 'int':
                $value = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'email':
                $value = filter_var($value, FILTER_SANITIZE_EMAIL);
                break;
            case 'string':
                $value = (string) $value;
                break;
            case 'array':
                $value = (array) $value;
                break;
            case 'date':
                $value = date($filter);
                break;
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


function getUserAgent() {
    return implode('.', [
        $_SERVER['HTTP_USER_AGENT'] ?? '',
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
        $_SERVER['HTTP_SEC_CH_UA'] ?? '',
    ]);
}


function minmax(int|float $value, int|float $min = 0, int|float $max = null) {

    if ($value < $min) {
        return $min;
    }

    if ($max && $value > $max) {
        return $max;
    }

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
                break;
        }
        array_push($msg, $prefix . $arg);
    }

    file_put_contents('php://stdout', implode("\n", $msg) . "\n");
}



function uuid4() {
    return Ramsey\Uuid\Uuid::uuid4()->toString();
}




/**
 * { function_description }
 *
 * @param      int    $value  The value
 * @param      int    $a      { parameter_description }
 * @param      int    $b      { parameter_description }
 *
 * @throws     Error  (description)
 *
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
