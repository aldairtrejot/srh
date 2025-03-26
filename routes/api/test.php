<?php

use App\Http\Controllers\ApiC;

Route::get('/status', [ApiC::class, 'status']); // Test de api