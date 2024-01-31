<?php

use App\Helpers\Config;


Config::setup([

    'company' => [
        'name'          => 'Company Name',
        'legalName'     => 'Company Name, LLC.',
        'phone'         => '555-555-1234',
        'email'         => 'email@company.name',
        'emailFrom'     => 'do-not-reply@company.name',
        'emailReply'    => 'replyto@company.name',

        'address' => [
            'street1'   => '123 Waffleton Way',
            'street2'   => 'Building 3, Suite 205',
            'city'      => 'Fauston',
            'state'     => 'AA',
            'zipcode'   => '55555',
        ],
    ],


    'app' => [
        'name'          => 'Photo Share',
        'established'   => 2023,
        'copyright'     => 'Gbox Studios',
        'url'           => 'http://localhost:3005',
    ],


    // @docs: https://github.com/tuupola/cors-middleware
    'cors' => [
        "origin" => [
            'http://localhost:3005',
            'http://localhost:5173',
            'https://gbox.name',
            'https://brandtley.name',
        ],
        "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE"],
        "headers.allow" => ["Authorization", "If-Match", "If-Unmodified-Since"],
        "headers.expose" => ["Etag"],
        "credentials" => true,
        "cache" => 86400,
    ],


    'registration' => [
        'min_age' => 18,
    ],


    'paths' => [
        'cache_dir'         => path('/storage/cache'),
        'database_dir'      => path('/storage/database'),
        'database_file'     => path('/storage/database/main.db'),
        'logs_dir'          => path('/storage/logs'),
        'migrations_dir'    => path('/migrations'),
        'sessions_dir'      => path('/storage/sessions'),
        'views_dir'         => path('/src/views'),
    ],


    'public_routes' => [
        // PUBLIC CONTENT PAGES
        'home'              => '/',
        'about'             => '/about',
        'privacy'           => '/privacy-policy',
        'terms'             => '/terms-of-use',

        // AUTH ROUTES
        'login'             => '/login',
        'logout'            => '/logout',
        'register'          => '/register',
        'passwordreset'     => '/password-reset',
        'verification'      => '/verification',

        // DASHBOARD ROUTES

    ],


    'sessions' => [
        // 'expires' => null,
    ],


    'emails' => [
        // NOTES: https://github.com/rnwood/smtp4dev/wiki/Configuring-Clients
        'smtp' => [
            'host'      => env('EMAIL_HOST'),
            'port'      => env('EMAIL_PORT',        25),
            'password'  => env('EMAIL_PASSWORD',    ''),
            'username'  => env('EMAIL_USERNAME',    ''),
            'enabled'   => env('EMAIL_ENABLED',     true),
            'secure'    => env('EMAIL_SECURE',      false),
            'auth'      => env('EMAIL_AUTH',        false),
            'autotls'   => env('EMAIL_AUTOTLS',     false),
            'debug'     => env('EMAIL_DEBUG',       false),
        ],
    ],
]);


[$year, $month, $day] = explode('-', date('Y-m-d'));

Config::set([
    'date' => [
        'year'  => $year,
        'month' => $month,
        'day'   => $day,
        'full'  => date('Y-m-d'),
    ],
]);
