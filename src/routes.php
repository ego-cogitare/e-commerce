<?php
    $app->post('/{_:logout|login}', '\Controllers\AuthController');
    
    $app->get('/', '\Controllers\IndexController::index');
    
    $app->get('/users', '\Controllers\UsersController::index');
    
    $app->get('/brand/list', '\Controllers\BrandController::index');
    $app->post('/brand/add', '\Controllers\BrandController::add');
    $app->post('/brand/add-picture', '\Controllers\BrandController::addPicture');
    $app->post('/brand/update/:id', '\Controllers\BrandController::update');
    $app->delete('/brand/delete/:id', '\Controllers\BrandController::delete');
    
    $app->post('/file/upload', '\Controllers\FileController');