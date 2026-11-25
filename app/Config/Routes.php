<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// 1. Dashboard, Live Search & Detail Solusi
// --------------------------------------------------------------------
$routes->get('/', 'DashboardController::index', ['as' => 'dashboard']);
$routes->get('search', 'DashboardController::search', ['as' => 'solutions.search']);
$routes->get('api/solutions/search', 'DashboardController::search', ['as' => 'api.solutions.search']);

// --------------------------------------------------------------------
// 2. Manajemen Solusi (CRUD & Quick Fix Pinning)
// --------------------------------------------------------------------
$routes->group('solutions', static function (RouteCollection $routes) {
    $routes->get('create', 'SolutionController::create', ['as' => 'solutions.create']);
    $routes->post('/', 'SolutionController::store', ['as' => 'solutions.store']);
    $routes->post('store', 'SolutionController::store');

    $routes->get('(:num)', 'DashboardController::detail/$1', ['as' => 'solutions.detail']);
    $routes->get('(:num)/edit', 'SolutionController::edit/$1', ['as' => 'solutions.edit']);

    $routes->post('(:num)/update', 'SolutionController::update/$1', ['as' => 'solutions.update']);
    $routes->put('(:num)', 'SolutionController::update/$1');

    $routes->post('(:num)/delete', 'SolutionController::delete/$1', ['as' => 'solutions.delete']);
    $routes->delete('(:num)', 'SolutionController::delete/$1');

    $routes->post('(:num)/toggle-pin', 'SolutionController::togglePin/$1', ['as' => 'solutions.togglePin']);
});

// --------------------------------------------------------------------
// 3. Manajemen Master Data (Kategori & Sub-Kategori)
// --------------------------------------------------------------------
$routes->group('master', static function (RouteCollection $routes) {
    $routes->get('/', 'MasterDataController::index', ['as' => 'master.index']);

    // Kategori
    $routes->post('categories', 'MasterDataController::storeCategory', ['as' => 'master.categories.store']);
    $routes->post('categories/(:num)/update', 'MasterDataController::updateCategory/$1', ['as' => 'master.categories.update']);
    $routes->put('categories/(:num)', 'MasterDataController::updateCategory/$1');
    $routes->post('categories/(:num)/delete', 'MasterDataController::deleteCategory/$1', ['as' => 'master.categories.delete']);
    $routes->delete('categories/(:num)', 'MasterDataController::deleteCategory/$1');

    // Sub-Kategori
    $routes->post('sub-categories', 'MasterDataController::storeSubCategory', ['as' => 'master.subcategories.store']);
    $routes->post('sub-categories/(:num)/update', 'MasterDataController::updateSubCategory/$1', ['as' => 'master.subcategories.update']);
    $routes->put('sub-categories/(:num)', 'MasterDataController::updateSubCategory/$1');
    $routes->post('sub-categories/(:num)/delete', 'MasterDataController::deleteSubCategory/$1', ['as' => 'master.subcategories.delete']);
    $routes->delete('sub-categories/(:num)', 'MasterDataController::deleteSubCategory/$1');
});
