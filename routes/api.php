<?php

use Illuminate\Support\Facades\Route;

// API_TEST_ROUTE
require __DIR__ . '/api/test.php';

// ALL_ROUTE
foreach (glob(__DIR__ . '/api/web/*/*/*.php') as $filename) {
    require $filename;
}