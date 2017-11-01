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
    $app->post('/brand/update/{id}', '\Controllers\BrandController::update');
    $app->post('/brand/remove/{id}', '\Controllers\BrandController::remove');
    
    /**
     * File processing routes
     */
    $app->post('/file/upload', '\Controllers\FileController');
    
    /**
     * Settings routes
     */
    $app->get('/settings/{action}', '\Controllers\SettingsController');
    $app->post('/settings/update/{action}', '\Controllers\SettingsController');
    
    /**
     * Category routes
     */
    $app->get('/category/list', '\Controllers\CategoryController::index');
    $app->get('/category/{action}', '\Controllers\CategoryController');
    $app->get('/category/get/{id}', '\Controllers\CategoryController::get');
    $app->post('/category/add', '\Controllers\CategoryController::add');
    $app->post('/category/update/{id}', '\Controllers\CategoryController::update');
    $app->post('/category/remove/{id}', '\Controllers\CategoryController::remove');
    
    /**
     * Product routes
     */
    $app->get('/product/list', '\Controllers\ProductController::index');
    $app->get('/product/bootstrap', '\Controllers\ProductController::bootstrap');
    $app->get('/product/get/{id}', '\Controllers\ProductController::get');
    $app->post('/product/update/{id}', '\Controllers\ProductController::update');
    $app->post('/product/remove/{id}', '\Controllers\ProductController::remove');
    $app->post('/product/add-picture/{id}', '\Controllers\ProductController::addPicture');