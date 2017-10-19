<?php
    $app->post('/{_:logout|login}', '\Controllers\AuthController');
    
    $app->get('/', '\Controllers\IndexController::index');