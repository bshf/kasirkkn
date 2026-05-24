<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', function() {
    return view('dashboard');
});
$routes->get('menu', 'MenuController::index');
$routes->post('menu/save', 'MenuController::save');
$routes->delete('menu/delete/(:num)', 'MenuController::delete/$1');

$routes->get('transaction', function() {
    return view('transaction');
});
