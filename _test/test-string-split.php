<?php

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

function array_query_explode(string $keyPath, array $data) {

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


function array_query_string_split(string $keyPath, array $data) {

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



// function _test($res, $expected) {
function _test(string $label, callable $cb) {
    $start      = microtime(true);
    $res        = $cb();
    $end        = microtime(true);

    return [
        $label,
        $start,
        $end,
        $end - $start, // $elapsed,
    ];
}


$data = [
    'person' => [
        'name' => [
            'first' => 'bob',
            'last'  => 'lawblah',
        ],
        'age'   => 43,
    ],
    'people' => [
        [ 'name' => 'Jeremy',   'age' => 34,    'city' => 'Boston, MA', ],
        [ 'name' => 'Gina',     'age' => 23,    'city' => 'Cityville, DA', ],
        [ 'name' => 'Caprise',  'age' => 74,    'city' => 'Amblerville, YI', ],
    ],
];



function iterate(string $label, int $iterations, callable $cb) {

    $time = 0;
    $label = '';

    for ($i=0; $i < $iterations; $i++) {
        $res = _test($label, $cb);

        $time += $res[3];
    }

    $precision = 4;

    echo '----' . PHP_EOL
        . $label . PHP_EOL
        . 'iterations   : ' . $iterations . PHP_EOL
        . 'total_time   : ' . number_format($time, $precision) . PHP_EOL
        . 'avg          : ' . number_format($time / $iterations, $precision) . PHP_EOL
        ;
}



// echo $test[4] . PHP_EOL;
$testString = 'people.0.city';

$iterations = 1000000;

iterate('explode', $iterations, function() use ($testString) {
    $res = explode('.', $testString);

    return $res;
});





iterate('string_parse', $iterations, function() use ($testString) {
    $res = [];
    $count = 0;
    for ($i=0; $i < strlen($testString); $i++) {
        $char = $testString[$i];

        if ($char === '.') {
            $count += 1;
            continue;
        }

        $res[$count] = $res[$count] ?? '';

        $res[$count] .= $char;
    }

    return $res;
});
