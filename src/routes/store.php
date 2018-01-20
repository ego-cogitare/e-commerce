<?php
    $app->get('/store/bootstrap', '\Controllers\Store\BootstrapController::index');
    $app->get('/store/brands', '\Controllers\Store\BrandController::index');
    $app->get('/store/products', '\Controllers\Store\ProductController::index');
    $app->get('/store/blog', '\Controllers\Store\BlogController::index');