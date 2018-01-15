<?php
    $app->get('/store/bootstrap', '\Controllers\Store\BootstrapController::index');
    $app->get('/store/brands', '\Controllers\Store\BrandController::index');