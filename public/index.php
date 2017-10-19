<?php
    ini_set('display_errors', 1);
    error_reporting(1);
    
    $app = include_once __DIR__ . '/../bootstrap.php';
    $app->run();
