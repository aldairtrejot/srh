<?php

use App\Http\Controllers\Letter\External\CloudExternalC;
use App\Http\Controllers\Letter\External\ExternalC;

//ROUTE EXTERNAK / CIRCULARES EXTERNAS
// GET
Route::get('/external', [ExternalC::class, 'list'])->name('external.list')->middleware('auth');
Route::get('/external/create', [ExternalC::class, 'create'])->name('external.create')->middleware('auth');
Route::get('/external/edit/{id}', [ExternalC::class, 'edit'])->name('external.edit')->middleware('auth');
Route::get('/external/cloud/{id}', [CloudExternalC::class, 'cloud'])->name('external.cloud')->middleware('auth');

// POST
Route::post('/external/table', [ExternalC::class, 'table'])->name('external.table')->middleware('auth');
Route::post('/external/save', [ExternalC::class, 'save'])->name('external.save')->middleware('auth');
Route::post('/external/collection/area', [ExternalC::class, 'area'])->name('external.collection.area')->middleware('auth');
Route::post('/external/unique', [ExternalC::class, 'unique'])->name('external.unique')->middleware('auth');
Route::post('/external/cloud/anexos', [CloudExternalC::class, 'list'])->name('external.cloud.anexos')->middleware('auth');
Route::post('/external/cloud/upload', [CloudExternalC::class, 'upload'])->name('external.cloud.upload')->middleware('auth');
Route::post('/external/cloud/delete', [CloudExternalC::class, 'delete'])->name('external.cloud.delete')->middleware('auth');
