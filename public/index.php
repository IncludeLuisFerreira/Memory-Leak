<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Http\Router;

session_start();

$router = new Router();

// Routes
$router->add('GET', '/', 'HomeController@index');
$router->add('GET', '/main', 'HomeController@main');

// Auth Routes
$router->add('GET', '/login', 'AuthController@showLogin');
$router->add('POST', '/login', 'AuthController@login');
$router->add('GET', '/cadastro', 'AuthController@showRegister');
$router->add('POST', '/cadastro', 'AuthController@register');
$router->add('GET', '/logout', 'AuthController@logout');

// Game Routes
$router->add('GET', '/menu', 'GameController@menu');
$router->add('GET', '/solo', 'GameController@solo');
$router->add('GET', '/online', 'GameController@online');
$router->add('GET', '/historico', 'GameController@historico');
$router->add('GET', '/api/ranking', 'GameController@ranking');
$router->add('GET', '/api/historico', 'GameController@historicoData');
$router->add('GET', '/api/status', 'GameController@status');
$router->add('POST', '/api/play', 'GameController@play');
$router->add('POST', '/api/compare', 'GameController@compare');
$router->add('POST', '/api/save', 'GameController@saveMatch');

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$router->dispatch($method, $uri);
