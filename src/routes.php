<?php
    $app->post('/{_:logout|login}', '\Controllers\AuthController');
    
    $app->get('/', '\Controllers\IndexController::index');
    
    $app->get('/users', '\Controllers\UsersController::index');
    
    $app->get('/brand/list', '\Controllers\BrandController::index');
    $app->get('/brand/get/{id}', '\Controllers\BrandController::get');
    $app->post('/brand/add', '\Controllers\BrandController::add');
    $app->post('/brand/add-picture', '\Controllers\BrandController::addPicture');
    $app->post('/brand/update/{id}', '\Controllers\BrandController::update');
    $app->post('/brand/remove/{id}', '\Controllers\BrandController::remove');
    
    $app->post('/file/upload', '\Controllers\FileController');