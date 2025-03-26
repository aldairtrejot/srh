<?php

use App\Http\Controllers\Home\AboutC;
use App\Http\Controllers\Home\DashboardC;

Route::get('/dashboard', [DashboardC::class, 'dashboard'])->name('dashboard')->middleware('auth'); //ROUTE_DASH BOARD
Route::get('/about', AboutC::class)->name('about')->middleware('auth'); //ROUTE_ABOUT

