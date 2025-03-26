<?php

use App\Http\Controllers\administration\RoleC;
use App\Http\Controllers\Administration\UserC;

//ROUTE_USER
//GET
Route::get('/user', UserC::class)->name('user.list')->middleware('auth'); //ROUTE_USER
Route::get('/user/list', [UserC::class, 'list'])->middleware('auth'); //ROUTE_LIST_OF_USER
Route::get('/user/create', [UserC::class, 'create'])->name('user.create')->middleware('auth'); //ROUTE_CREATE
Route::get('/user/edit/{id}', [UserC::class, 'edit'])->name('user.edit')->middleware('auth');

//POST
Route::post('/user/save', [UserC::class, 'save'])->name('user.save')->middleware('auth');
Route::post('/user/validatePassword', [UserC::class, 'validatePassword'])->name('user.validatePassword')->middleware('auth');
Route::post('/user/changePassword', [UserC::class, 'changePassword'])->name('user.changePassword')->middleware('auth');

// ROUTE_ROLE
// GET
Route::get('/role', RoleC::class)->name('role.list')->middleware('auth'); //ROUTE_VIEW_ROLE
