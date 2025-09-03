<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Application;


// Load env variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Create application instance
$app = new Application(__DIR__ . '/..');

// Define routes 
$app->get('/', 'HomeController@index');
$app->get('/about', 'HomeController@about');
$app-get('/users/{id}', 'UserController@show');
$app->post('/users', 'UserController@store');


// API routes
$app->get('/api/users', function($request) {
    return new Core\Http\Response()->json(['users' => []]);
});

// Run the application
$app->run();