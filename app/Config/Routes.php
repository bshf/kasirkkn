<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'DashboardController::index');
$routes->get('dashboard', 'DashboardController::index');
$routes->get('menu', 'MenuController::index');
$routes->get('transaction', 'TransactionController::index');
$routes->post('menu/save', 'MenuController::save');
$routes->delete('menu/delete/(:num)', 'MenuController::delete/$1');
$routes->get('menu/get_all_json', 'MenuController::get_all_json');
$routes->post('transaction/save', 'TransactionController::save');
$routes->get('transaction/get_all_json', 'TransactionController::get_all_json');
$routes->get('transaction/get_detail_json/(:num)', 'TransactionController::get_detail_json/$1');