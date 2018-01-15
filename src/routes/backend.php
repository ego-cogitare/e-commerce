<?php
    /**
     * Default admin page route
     */
    $app->get('/', '\Controllers\Backend\IndexController::index');

    /**
     * Authorization routes
     */
    $app->post('/{_:logout|login}', '\Controllers\Backend\AuthController');

    /**
     * Brand CRUD routers
     */
    $app->get('/brand/list', '\Controllers\Backend\BrandController::index');
    $app->get('/brand/get/{id}', '\Controllers\Backend\BrandController::get');
    $app->post('/brand/add', '\Controllers\Backend\BrandController::add');
    $app->post('/brand/add-picture', '\Controllers\Backend\BrandController::addPicture');
    $app->post('/brand/delete-picture', '\Controllers\Backend\BrandController::deletePicture');
    $app->post('/brand/update/{id}', '\Controllers\Backend\BrandController::update');
    $app->post('/brand/remove/{id}', '\Controllers\Backend\BrandController::remove');

    /**
     * File processing routes
     */
    $app->post('/file/upload', '\Controllers\Backend\FileController');

    /**
     * Settings routes
     */
    $app->map(['GET', 'POST'], '/settings/{action}', '\Controllers\Backend\SettingsController');
    $app->post('/settings/update/{action}', '\Controllers\Backend\SettingsController');

    /**
     * Category routes
     */
    $app->get('/category/list', '\Controllers\Backend\CategoryController::index');
    $app->map(['GET', 'POST'], '/category/tree', '\Controllers\Backend\CategoryController');
    $app->get('/category/bootstrap', '\Controllers\Backend\CategoryController::bootstrap');
    $app->get('/category/get/{id}', '\Controllers\Backend\CategoryController::get');
    $app->post('/category/add-picture/{id}', '\Controllers\Backend\CategoryController::addPicture');
    $app->post('/category/delete-picture', '\Controllers\Backend\CategoryController');
    $app->post('/category/update/{id}', '\Controllers\Backend\CategoryController::update');
    $app->post('/category/remove/{id}', '\Controllers\Backend\CategoryController::remove');
    
    /**
     * Menu routes
     */
    $app->get('/menu/list', '\Controllers\Backend\MenuController::index');
    $app->get('/menu/{menuId}/get', '\Controllers\Backend\MenuController');
    $app->post('/menu/{menuId}/update', '\Controllers\Backend\MenuController');
    $app->post('/menu/{menuId}/remove', '\Controllers\Backend\MenuController');
    $app->post('/menu/{menuId}/item-add', '\Controllers\Backend\MenuController::itemAdd');
    $app->post('/menu/{menuId}/item-update/{id}', '\Controllers\Backend\MenuController::itemUpdate');
    $app->post('/menu/{menuId}/item-remove/{id}', '\Controllers\Backend\MenuController::itemRemove');

    /**
     * Product routes
     */
    $app->get('/product/list', '\Controllers\Backend\ProductController::index');
    $app->get('/product/bootstrap', '\Controllers\Backend\ProductController::bootstrap');
    $app->get('/product/get/{id}', '\Controllers\Backend\ProductController::get');
    $app->post('/product/add-picture/{id}', '\Controllers\Backend\ProductController::addPicture');
    $app->post('/product/delete-picture', '\Controllers\Backend\ProductController');
    $app->post('/product/update/{id}', '\Controllers\Backend\ProductController::update');
    $app->post('/product/remove/{id}', '\Controllers\Backend\ProductController::remove');
    $app->get('/product/properties', '\Controllers\Backend\ProductController::properties');
    $app->post('/product/add-property', '\Controllers\Backend\ProductController::addProperty');
    $app->post('/product/update-property/{id}', '\Controllers\Backend\ProductController::updateProperty');
    $app->post('/product/remove-property/{id}', '\Controllers\Backend\ProductController::removeProperty');

    /**
     * Static pages routes
     */
    $app->get('/page/list', '\Controllers\Backend\PageController::index');
    $app->get('/page/get/{id}', '\Controllers\Backend\PageController::get');
    $app->post('/page/add', '\Controllers\Backend\PageController::add');
    $app->post('/page/update/{id}', '\Controllers\Backend\PageController::update');
    $app->post('/page/remove/{id}', '\Controllers\Backend\PageController::remove');

    /**
     * Blog routes
     */
    $app->get('/blog/list', '\Controllers\Backend\BlogController::index');
    $app->get('/blog/get/{id}', '\Controllers\Backend\BlogController::get');
    $app->post('/blog/add', '\Controllers\Backend\BlogController::add');
    $app->post('/blog/update/{id}', '\Controllers\Backend\BlogController::update');
    $app->post('/blog/remove/{id}', '\Controllers\Backend\BlogController::remove');

    /**
     * Orders routes
     */
    $app->get('/order/list', '\Controllers\Backend\OrderController::index');
    $app->get('/order/get/{id}', '\Controllers\Backend\OrderController::get');
    $app->post('/order/add', '\Controllers\Backend\OrderController::add');
    $app->post('/order/update/{id}', '\Controllers\Backend\OrderController::update');
    $app->post('/order/remove/{id}', '\Controllers\Backend\OrderController::remove');
