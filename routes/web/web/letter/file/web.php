<?php

use App\Http\Controllers\Letter\File\FileC;
use App\Http\Controllers\Letter\File\CloudFileC;

//ROUTE file / EXPEDIENTES
// GET
Route::get('/file', [FileC::class, 'list'])->name('file.list')->middleware('auth');
Route::get('/file/create', [FileC::class, 'create'])->name('file.create')->middleware('auth');
Route::get('/file/edit/{id}', [FileC::class, 'edit'])->name('file.edit')->middleware('auth');
Route::get('/file/cloud/{id}', [FileC::class, 'cloud'])->name('file.cloud')->middleware('auth');


// POST
Route::post('/file/table', [FileC::class, 'table'])->name('file.table')->middleware('auth');
Route::post('/file/save', [FileC::class, 'save'])->name('file.save')->middleware('auth');
Route::post('/file/cloud/data', [CloudFileC::class, 'cloudData'])->name('file.cloud.data')->middleware('auth');
Route::post('/file/cloud/anexos', [CloudFileC::class, 'cloudAnexos'])->name('file.cloud.anexos')->middleware('auth');
Route::post('/file/cloud/oficios', [CloudFileC::class, 'cloudOficios'])->name('file.cloud.oficios')->middleware('auth');
Route::post('/file/cloud/upload', [CloudFileC::class, 'upload'])->name('file.cloud.upload')->middleware('auth');
Route::post('/file/cloud/delete', [CloudFileC::class, 'delete'])->name('file.cloud.delete')->middleware('auth');

