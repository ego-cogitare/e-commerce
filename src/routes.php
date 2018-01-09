<?php
    /**
     * Default admin page route
     */
    $app->get('/', '\Controllers\IndexController::index');

    /**
     * Authorization routes
     */
    $app->post('/{_:logout|login}', '\Controllers\AuthController');

    /**
     * Brand CRUD routers
     */
    $app->get('/brand/list', '\Controllers\BrandController::index');
    $app->get('/brand/get/{id}', '\Controllers\BrandController::get');
    $app->post('/brand/add', '\Controllers\BrandController::add');
    $app->post('/brand/add-picture', '\Controllers\BrandController::addPicture');
    $app->post('/brand/delete-picture', '\Controllers\BrandController::deletePicture');
    $app->post('/brand/update/{id}', '\Controllers\BrandController::update');
    $app->post('/brand/remove/{id}', '\Controllers\BrandController::remove');

    /**
     * File processing routes
     */
    $app->post('/file/upload', '\Controllers\FileController');

    /**
     * Settings routes
     */
    $app->map(['GET', 'POST'], '/settings/{action}', '\Controllers\SettingsController');
    $app->post('/settings/update/{action}', '\Controllers\SettingsController');

    /**
     * Category routes
     */
    $app->get('/category/list', '\Controllers\CategoryController::index');
    $app->map(['GET', 'POST'], '/category/tree', '\Controllers\CategoryController');
    $app->get('/category/get/{id}', '\Controllers\CategoryController::get');
    $app->post('/category/add-picture/{id}', '\Controllers\CategoryController::addPicture');
    $app->post('/category/delete-picture', '\Controllers\CategoryController');
    $app->post('/category/add', '\Controllers\CategoryController::add');
    $app->post('/category/update/{id}', '\Controllers\CategoryController::update');
    $app->post('/category/remove/{id}', '\Controllers\CategoryController::remove');

    /**
     * Product routes
     */
    $app->get('/product/list', '\Controllers\ProductController::index');
    $app->get('/product/bootstrap', '\Controllers\ProductController::bootstrap');
    $app->get('/product/get/{id}', '\Controllers\ProductController::get');
    $app->post('/product/add-picture/{id}', '\Controllers\ProductController::addPicture');
    $app->post('/product/delete-picture', '\Controllers\ProductController');
    $app->post('/product/update/{id}', '\Controllers\ProductController::update');
    $app->post('/product/remove/{id}', '\Controllers\ProductController::remove');

    /**
     * Static pages routes
     */
    $app->get('/page/list', '\Controllers\PageController::index');
    $app->get('/page/get/{id}', '\Controllers\PageController::get');
    $app->post('/page/add', '\Controllers\PageController::add');
    $app->post('/page/update/{id}', '\Controllers\PageController::update');
    $app->post('/page/remove/{id}', '\Controllers\PageController::remove');

    /**
     * Blog routes
     */
    $app->get('/blog/list', '\Controllers\BlogController::index');
    $app->get('/blog/get/{id}', '\Controllers\BlogController::get');
    $app->post('/blog/add', '\Controllers\BlogController::add');
    $app->post('/blog/update/{id}', '\Controllers\BlogController::update');
    $app->post('/blog/remove/{id}', '\Controllers\BlogController::remove');

    /**
     * Orders routes
     */
    $app->get('/order/list', '\Controllers\OrderController::index');
    $app->get('/order/get/{id}', '\Controllers\OrderController::get');
    $app->post('/order/add', '\Controllers\OrderController::add');
    $app->post('/order/update/{id}', '\Controllers\OrderController::update');
    $app->post('/order/remove/{id}', '\Controllers\OrderController::remove');
