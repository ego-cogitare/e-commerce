<?php
    $app->post('/{_:logout|login}', '\Controllers\AuthController');
    
    $app->get('/', '\Controllers\IndexController::index');
    
    $app->get('/users', '\Controllers\UsersController::index');
    
    $app->post('/file/upload', '\Controllers\FileController');