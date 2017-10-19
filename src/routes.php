<?php
    $app->post('/{_:logout|login}', '\Controllers\AuthController');
    
    $app->get('/admin', 'Admin\Controllers\IndexController::index');
    

