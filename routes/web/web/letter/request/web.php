<?php

use App\Http\Controllers\Letter\Request\RequestC;

// REQUEST
// GET
Route::get('/request', [RequestC::class, 'list'])->name('request.list')->middleware('auth');
Route::get('/request/create', [RequestC::class, 'create'])->name('request.create')->middleware('auth');
Route::get('/request/edit/{id}', [RequestC::class, 'edit'])->name('request.edit')->middleware('auth');

// POST
Route::post('/request/table', [RequestC::class, 'table'])->name('request.table')->middleware('auth');
Route::post('/request/save', [RequestC::class, 'save'])->name('request.save')->middleware('auth');
Route::post('/request/saveFile', [RequestC::class, 'saveFile'])->name('request.saveFile')->middleware('auth');
Route::post('/request/deleteFile', [RequestC::class, 'deleteFile'])->name('request.deleteFile')->middleware('auth');
