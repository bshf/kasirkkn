<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', function () {
    return redirect()->to(base_url('login'));
});
$routes->get('login',         'AuthController::index');
$routes->post('login/attempt', 'AuthController::attempt');
$routes->get('logout',        'AuthController::logout');


$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('dashboard/get_stats',                'DashboardController::get_stats');
    $routes->get('dashboard/get_weekly_chart',         'DashboardController::get_weekly_chart');
    $routes->get('dashboard/get_payment_breakdown',    'DashboardController::get_payment_breakdown');
    $routes->get('dashboard/get_recent_transactions',  'DashboardController::get_recent_transactions');

    $routes->get('menu', 'MenuController::index');
    $routes->post('menu/save', 'MenuController::save');
    $routes->delete('menu/delete/(:num)', 'MenuController::delete/$1');
    $routes->get('menu/get_all_json', 'MenuController::get_all_json');
    
    $routes->get('transaction', 'TransactionController::index');
    $routes->post('transaction/save', 'TransactionController::save');
    $routes->get('transaction/get_all_json', 'TransactionController::get_all_json');
    $routes->get('transaction/get_detail_json/(:num)', 'TransactionController::get_detail_json/$1');
    $routes->delete('transaction/delete/(:num)', 'TransactionController::delete/$1');
    $routes->get('transaction/struk/(:num)', 'TransactionController::struk/$1');
});
