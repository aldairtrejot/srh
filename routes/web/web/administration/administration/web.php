<?php

use App\Http\Controllers\Administration\UserC;

//ROUTE_USER
Route::get('/user', UserC::class)->name('user.list')->middleware('auth'); //ROUTE_USER
Route::get('/user/list', [UserC::class, 'list'])->middleware('auth'); //ROUTE_LIST_OF_USER
Route::get('/user/create', [UserC::class, 'create'])->name('user.create')->middleware('auth'); //ROUTE_CREATE
Route::post('/user/save', [UserC::class, 'save'])->name('user.save')->middleware('auth');
Route::get('/user/edit/{id}', [UserC::class, 'edit'])->name('user.edit')->middleware('auth');
Route::post('/user/validatePassword', [UserC::class, 'validatePassword'])->name('user.validatePassword')->middleware('auth');
Route::post('/user/changePassword', [UserC::class, 'changePassword'])->name('user.changePassword')->middleware('auth');
