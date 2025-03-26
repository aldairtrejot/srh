<?php

use App\Http\Controllers\Letter\Inside\InsideC;
use App\Http\Controllers\Letter\Inside\CloudInsideC;
use App\Http\Controllers\Letter\Inside\ReportInsideC;

//ROUTE INSIDE
// GET
Route::get('/inside', [InsideC::class, 'list'])->name('inside.list')->middleware('auth');
Route::get('/inside/create', [InsideC::class, 'create'])->name('inside.create')->middleware('auth');
Route::get('/inside/cloud/{id}', [InsideC::class, 'cloud'])->name('inside.cloud')->middleware('auth');
Route::get('/inside/edit/{id}', [InsideC::class, 'edit'])->name('inside.edit')->middleware('auth');
Route::get('/inside/generate-pdf/{id}', [ReportInsideC::class, 'report'])->middleware('auth');

// POST
Route::post('/inside/table', [InsideC::class, 'table'])->name('inside.table')->middleware('auth');
Route::post('/inside/save', [InsideC::class, 'save'])->name('inside.save')->middleware('auth');
Route::post('/inside/cloud/data', [CloudInsideC::class, 'cloudData'])->name('inside.cloud.data')->middleware('auth');
Route::post('/inside/cloud/anexos', [CloudInsideC::class, 'cloudAnexos'])->name('inside.cloud.anexos')->middleware('auth');
Route::post('/inside/cloud/oficios', [CloudInsideC::class, 'cloudOficios'])->name('inside.cloud.oficios')->middleware('auth');
Route::post('/inside/cloud/upload', [CloudInsideC::class, 'upload'])->name('inside.cloud.upload')->middleware('auth');
Route::post('/inside/cloud/delete', [CloudInsideC::class, 'delete'])->name('inside.cloud.delete')->middleware('auth');
