<?php

use App\Http\Controllers\Letter\Round\RoundC;
use App\Http\Controllers\Letter\Round\CloudRoundC;
use App\Http\Controllers\Letter\Report\ReporteTemplateC;

//ROUTE ROUND / CIRCULARES
// GET
Route::get('/round/create', [RoundC::class, 'create'])->name('round.create')->middleware('auth');
Route::get('/round', [RoundC::class, 'list'])->name('round.list')->middleware('auth');
Route::get('/round/edit/{id}', [RoundC::class, 'edit'])->name('round.edit')->middleware('auth');
Route::get('/round/cloud/{id}', [RoundC::class, 'cloud'])->name('round.cloud')->middleware('auth');
Route::get('/round/generate-pdf/{id}', [ReporteTemplateC::class, 'round'])->middleware('auth');
Route::get('/file/generate-pdf/{id}', [ReporteTemplateC::class, 'file'])->middleware('auth');
Route::get('/office/generate-pdf/{id}', [ReporteTemplateC::class, 'office'])->middleware('auth');

//POST
Route::post('/round/table', [RoundC::class, 'table'])->name('round.table')->middleware('auth');
Route::post('/round/save', [RoundC::class, 'save'])->name('round.save')->middleware('auth');
Route::post('/round/cloud/data', [CloudRoundC::class, 'cloudData'])->name('round.cloud.data')->middleware('auth');
Route::post('/round/cloud/anexos', [CloudRoundC::class, 'cloudAnexos'])->name('round.cloud.anexos')->middleware('auth');
Route::post('/round/cloud/oficios', [CloudRoundC::class, 'cloudOficios'])->name('round.cloud.oficios')->middleware('auth');
Route::post('/round/cloud/upload', [CloudRoundC::class, 'upload'])->name('round.cloud.upload')->middleware('auth');
Route::post('/round/cloud/delete', [CloudRoundC::class, 'delete'])->name('round.cloud.delete')->middleware('auth');

