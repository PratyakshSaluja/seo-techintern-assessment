<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Sitemap Route
$routes->get('sitemap.xml', 'SitemapController::index');

// SEO-friendly routes for categories and products
$routes->get('category/(:segment)', 'CategoryController::show/$1');
$routes->get('product/(:segment)', 'ProductController::show/$1');

// Define routes for the Admin panel
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], static function ($routes) {
    // Redirect base /admin to /admin/categories
    $routes->get('/', 'CategoriesController::index'); // Or redirect: $routes->redirect('/', 'admin/categories');

    // Category Management
    $routes->get('categories', 'CategoriesController::index');
    $routes->get('categories/new', 'CategoriesController::new');
    $routes->post('categories/create', 'CategoriesController::create');
    $routes->get('categories/edit/(:num)', 'CategoriesController::edit/$1');
    $routes->post('categories/update/(:num)', 'CategoriesController::update/$1');
    $routes->get('categories/delete/(:num)', 'CategoriesController::delete/$1'); // Use POST/DELETE for actual deletion in production

    // Product Management
    $routes->get('products', 'ProductsController::index');
    $routes->get('products/new', 'ProductsController::new');
    $routes->post('products/create', 'ProductsController::create');
    $routes->get('products/edit/(:num)', 'ProductsController::edit/$1');
    $routes->post('products/update/(:num)', 'ProductsController::update/$1');
    $routes->get('products/delete/(:num)', 'ProductsController::delete/$1'); // Use POST/DELETE for actual deletion in production
});
