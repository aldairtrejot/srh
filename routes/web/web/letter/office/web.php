<?php

use App\Http\Controllers\Letter\Office\CloudC;
use App\Http\Controllers\Letter\Office\OfficeC;

//ROUTE OFICIOS
// GET
Route::get('/office', [OfficeC::class, 'list'])->name('office.list')->middleware('auth');
Route::get('/office/create', [OfficeC::class, 'create'])->name('office.create')->middleware('auth');
Route::get('/office/edit/{id}', [OfficeC::class, 'edit'])->name('office.edit')->middleware('auth');
Route::get('/office/cloud/{id}', [OfficeC::class, 'cloud'])->name('office.cloud')->middleware('auth');


// POST
Route::post('/office/table', [OfficeC::class, 'table'])->name('office.table')->middleware('auth');
Route::post('/office/save', [OfficeC::class, 'save'])->name('office.save')->middleware('auth');
Route::post('/office/cloud/data', [CloudC::class, 'cloudData'])->name('office.cloud.data')->middleware('auth');
Route::post('/office/cloud/anexos', [CloudC::class, 'cloudAnexos'])->name('office.cloud.anexos')->middleware('auth');
Route::post('/office/cloud/oficios', [CloudC::class, 'cloudOficios'])->name('office.cloud.oficios')->middleware('auth');
Route::post('/office/cloud/upload', [CloudC::class, 'upload'])->name('office.cloud.upload')->middleware('auth');
Route::post('/office/cloud/delete', [CloudC::class, 'delete'])->name('office.cloud.delete')->middleware('auth');
Route::post('/office/validate/folGestion', [OfficeC::class, 'validateFol'])->name('office.validate.folGestion')->middleware('auth');
