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
     * Product routes
     */
    $app->get('/product/{action}[/{id}]', '\Controllers\ProductController');
    
    /**
     * Category routes
     */
    $app->get('/category/{action}[/{id}]', '\Controllers\CategoryController');