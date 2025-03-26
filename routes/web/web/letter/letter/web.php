<?php

use App\Http\Controllers\Letter\Letter\LetterC;
use App\Http\Controllers\Letter\Collection\CollectionAreaC;
use App\Http\Controllers\Letter\Collection\CollectionUnidadC;
use App\Http\Controllers\Letter\Collection\CollectionTramiteC;
use App\Http\Controllers\Letter\Collection\CollectionClaveC;
use App\Http\Controllers\Letter\Report\ReporteCorrespondenciaC;
use App\Http\Controllers\Letter\Dashboard\DashboardLetterC;
use App\Http\Controllers\Letter\Letter\CloudLetterC;

//ROUTE_LETTER
//GET
Route::get('/letter/dashboard', [LetterC::class, 'dashboard'])->name('letter.dashboard')->middleware('auth');
Route::get('/letter', LetterC::class)->name('letter.list')->middleware('auth');
Route::get('/letter/delete', [LetterC::class . 'delete'])->name('letter.delete')->middleware('auth');
Route::get('/letter/table', [LetterC::class, 'table'])->name('letter.table')->middleware('auth');
Route::get('/letter/create', [LetterC::class, 'create'])->name('letter.create')->middleware('auth');
Route::get('/letter/edit/{id}', [LetterC::class, 'edit'])->name('letter.edit')->middleware('auth');
Route::get('/letter/cloud/{id}', [LetterC::class, 'cloud'])->name('letter.cloud')->middleware('auth');
Route::get('/letter/generate-pdf/correspondencia/{id}', [ReporteCorrespondenciaC::class, 'generatePdf'])->middleware('auth');
Route::get('/other/generate-pdf/office/{id}', [ReporteCorrespondenciaC::class, 'generatePdf'])->middleware('auth'); //GENERACION DE REPORTE

//POST
Route::post('/letter/tableCopy', [LetterC::class, 'tableCopy'])->name('letter.tableCopy')->middleware('auth');
Route::post('/letter/save', [LetterC::class, 'save'])->name('letter.save')->middleware('auth');
Route::post('/letter/collection/validateUnique', [LetterC::class, 'validateUnique'])->name('letter.validateUnique')->middleware('auth');
Route::post('/letter/collection/uniqueRemitente', [LetterC::class, 'uniqueRemitente'])->name('letter.collection.uniqueRemitente')->middleware('auth');
Route::post('/letter/collection/uniqueNameValidate', [LetterC::class, 'uniqueRemitenteName'])->name('letter.collection.uniqueRemitenteName')->middleware('auth');
Route::post('/letter/delete/copy', [LetterC::class, 'deleteCopy'])->name('letter.deleteCopy')->middleware('auth');
Route::post('/letter/collection/area', [LetterC::class, 'collectionArea'])->name('letter.collectionArea')->middleware('auth');
Route::post('/letter/saveCopy', [LetterC::class, 'saveCopy'])->name('letter.saveCopy')->middleware('auth');
Route::post('/letter/validateCopy', [LetterC::class, 'validateCopy'])->name('letter.validateCopy')->middleware('auth');
Route::post('/letter/collection/collectionArea', [CollectionAreaC::class, 'collection'])->name('letter.collection.area')->middleware('auth');
Route::post('/collection/areaAndUser', [CollectionAreaC::class, 'getUserArea'])->middleware('auth');  // CONSULTA DE USUARIO, ENLACE Y AREA
Route::post('/collection/validate/letter', [CollectionAreaC::class, 'getletter'])->middleware('auth'); // VALIDACION DE NO DE CORRESPONDENCIA
Route::post('/valitade/letter', [LetterC::class, 'getletter'])->middleware('auth'); // TRAE INFORMACION COMO EL NO DE CORRESPONDENCIA QUE EXISTA ASI COMO USUAIRO
Route::post('/collection/area/consecutivo', [CollectionAreaC::class, 'areaAutoincrement'])->middleware('auth'); // CONSECUTIVO DE AREA
Route::post('/letter/collection/collectionUnidad', [CollectionUnidadC::class, 'collection'])->name('letter.collection.unidad')->middleware('auth');
Route::post('/letter/collection/collectionTramite', [CollectionTramiteC::class, 'collection'])->name('letter.collection.tramite')->middleware('auth');
Route::post('/letter/collection/collectionClave', [CollectionClaveC::class, 'collection'])->name('letter.collection.clabe')->middleware('auth');
Route::post('/letter/collection/dataClave', [CollectionClaveC::class, 'dataClave'])->name('letter.collection.dataClave')->middleware('auth');
Route::post('/letter/dashboard/getCollection', [DashboardLetterC::class, 'getCollection'])->name('letter.dashboard.getCollection')->middleware('auth'); // Letter Dashboard
Route::post('/letter/dashboard/generate', [DashboardLetterC::class, 'generate'])->name('letter.dashboard.generate')->middleware('auth'); // Letter Dashboard
Route::post('/letter/cloud/data', [CloudLetterC::class, 'cloudData'])->name('letter.cloud.data')->middleware('auth'); ////Cloud
Route::post('/letter/cloud/anexos', [CloudLetterC::class, 'cloudAnexos'])->name('letter.cloud.anexos')->middleware('auth'); ////Cloud
Route::post('/letter/cloud/upload', [CloudLetterC::class, 'upload'])->name('letter.cloud.upload')->middleware('auth'); ////Cloud
Route::post('/letter/cloud/delete', [CloudLetterC::class, 'delete'])->name('letter.cloud.delete')->middleware('auth'); ////Cloud
