<?php

use App\Http\Controllers\Letter\File\CloudFileC;
use App\Http\Controllers\Letter\Round\CloudRoundC;
use App\Http\Controllers\Cloud\AlfrescoC;
use App\Http\Controllers\Letter\Collection\CollectionYearC;
use App\Http\Controllers\Letter\File\FileC;
use App\Http\Controllers\Letter\Inside\CloudInsideC;
use App\Http\Controllers\Letter\Letter\CloudLetterC;
use App\Http\Controllers\Letter\Office\CloudC;
use App\Http\Controllers\Letter\Inside\InsideC;
use App\Http\Controllers\Letter\Office\OfficeC;
use App\Http\Controllers\Letter\Collection\CollectionClaveC;
use App\Http\Controllers\Letter\Collection\CollectionTramiteC;
use App\Http\Controllers\Letter\Collection\CollectionUnidadC;
use App\Http\Controllers\Administration\LoginC;
use App\Http\Controllers\Administration\RecoverC;
use App\Http\Controllers\Administration\RegisterC;
use App\Http\Controllers\Administration\UserC;
use App\Http\Controllers\Home\AboutC;
use App\Http\Controllers\Home\DashboardC;
use App\Http\Controllers\Letter\Collection\CollectionAreaC;
use App\Http\Controllers\Letter\Letter\LetterC;
use App\Http\Controllers\Letter\Report\ReporteCorrespondenciaC;
use App\Http\Controllers\Letter\Report\ReporteTemplateC;
use App\Http\Controllers\Letter\Round\RoundC;
use Illuminate\Support\Facades\Route;

Route::get('/login', LoginC::class)->name('login'); ///ROUTE_LOGIN
Route::get('/register', RegisterC::class)->name('register'); ///ROUTE_REGISTER
Route::get('/recover', RecoverC::class)->name('recover');//ROUTE_RECOVER
Route::post('/login', [LoginC::class, 'authenticate']);///ROUTE_AUTHENTICATE

///IS_PROTECT
Route::get('/dashboard', [DashboardC::class, 'dashboard'])->name('dashboard')->middleware('auth'); //ROUTE_DASH BOARD
Route::get('/about', AboutC::class)->name('about')->middleware('auth'); //ROUTE_ABOUT
Route::post('/logout', [LoginC::class, 'logout'])->name('logout')->middleware('auth');//ROUTE_LOGOUT

//ROUTE_USER
Route::get('/user', UserC::class)->name('user.list')->middleware('auth'); //ROUTE_USER
Route::get('/user/list', [UserC::class, 'list'])->middleware('auth'); //ROUTE_LIST_OF_USER
Route::get('/user/create', [UserC::class, 'create'])->name('user.create')->middleware('auth'); //ROUTE_CREATE
Route::post('/user/save', [UserC::class, 'save'])->name('user.save')->middleware('auth');
Route::get('/user/edit/{id}', [UserC::class, 'edit'])->name('user.edit')->middleware('auth');
Route::post('/user/validatePassword', [UserC::class, 'validatePassword'])->name('user.validatePassword')->middleware('auth');
Route::post('/user/changePassword', [UserC::class, 'changePassword'])->name('user.changePassword')->middleware('auth');

//ROUTE_LETTER
Route::get('/letter/list', LetterC::class)->name('letter.list')->middleware('auth');
Route::get('/letter/delete', [LetterC::class . 'delete'])->name('letter.delete')->middleware('auth');
Route::get('/letter/table', [LetterC::class, 'table'])->name('letter.table')->middleware('auth');
Route::get('/letter/create', [LetterC::class, 'create'])->name('letter.create')->middleware('auth');
Route::get('/letter/edit/{id}', [LetterC::class, 'edit'])->name('letter.edit')->middleware('auth');
Route::post('/letter/save', [LetterC::class, 'save'])->name('letter.save')->middleware('auth');
Route::post('/letter/collection/collectionArea', [CollectionAreaC::class, 'collection'])->name('letter.collection.area')->middleware('auth');
Route::post('/letter/collection/collectionUnidad', [CollectionUnidadC::class, 'collection'])->name('letter.collection.unidad')->middleware('auth');
Route::post('/letter/collection/collectionTramite', [CollectionTramiteC::class, 'collection'])->name('letter.collection.tramite')->middleware('auth');
Route::post('/letter/collection/collectionClave', [CollectionClaveC::class, 'collection'])->name('letter.collection.clabe')->middleware('auth');
Route::post('/letter/collection/dataClave', [CollectionClaveC::class, 'dataClave'])->name('letter.collection.dataClave')->middleware('auth');
Route::get('/letter/generate-pdf/correspondencia/{id}', [ReporteCorrespondenciaC::class, 'generatePdf'])->middleware('auth');
Route::post('/letter/collection/validateUnique', [LetterC::class, 'validateUnique'])->name('letter.validateUnique')->middleware('auth');
Route::post('/letter/collection/uniqueRemitente', [LetterC::class, 'uniqueRemitente'])->name('letter.collection.uniqueRemitente')->middleware('auth');

////Cloud
Route::get('/letter/cloud/{id}', [LetterC::class, 'cloud'])->name('letter.cloud')->middleware('auth');
Route::post('/letter/cloud/data', [CloudLetterC::class, 'cloudData'])->name('letter.cloud.data')->middleware('auth');
Route::post('/letter/cloud/anexos', [CloudLetterC::class, 'cloudAnexos'])->name('letter.cloud.anexos')->middleware('auth');
Route::post('/letter/cloud/upload', [CloudLetterC::class, 'upload'])->name('letter.cloud.upload')->middleware('auth');
Route::post('/letter/cloud/delete', [CloudLetterC::class, 'delete'])->name('letter.cloud.delete')->middleware('auth');
// --- -- --- - -- - -- --

//ROUTE OFICIOS
Route::get('/office/list', [OfficeC::class, 'list'])->name('office.list')->middleware('auth');
Route::post('/office/table', [OfficeC::class, 'table'])->name('office.table')->middleware('auth');
Route::get('/office/create', [OfficeC::class, 'create'])->name('office.create')->middleware('auth');
Route::get('/office/edit/{id}', [OfficeC::class, 'edit'])->name('office.edit')->middleware('auth');
Route::post('/office/save', [OfficeC::class, 'save'])->name('office.save')->middleware('auth');
Route::get('/office/cloud/{id}', [OfficeC::class, 'cloud'])->name('office.cloud')->middleware('auth');
Route::post('/office/cloud/data', [CloudC::class, 'cloudData'])->name('office.cloud.data')->middleware('auth');
Route::post('/office/cloud/anexos', [CloudC::class, 'cloudAnexos'])->name('office.cloud.anexos')->middleware('auth');
Route::post('/office/cloud/oficios', [CloudC::class, 'cloudOficios'])->name('office.cloud.oficios')->middleware('auth');
Route::post('/office/cloud/upload', [CloudC::class, 'upload'])->name('office.cloud.upload')->middleware('auth');
Route::post('/office/cloud/delete', [CloudC::class, 'delete'])->name('office.cloud.delete')->middleware('auth');
Route::get('/office/generate-pdf/{id}', [ReporteTemplateC::class, 'office'])->middleware('auth');

//ROUTE INSIDE
Route::get('/inside/list', [InsideC::class, 'list'])->name('inside.list')->middleware('auth');
Route::post('/inside/table', [InsideC::class, 'table'])->name('inside.table')->middleware('auth');
Route::get('/inside/create', [InsideC::class, 'create'])->name('inside.create')->middleware('auth');
Route::get('/inside/edit/{id}', [InsideC::class, 'edit'])->name('inside.edit')->middleware('auth');
Route::post('/inside/save', [InsideC::class, 'save'])->name('inside.save')->middleware('auth');
Route::get('/inside/cloud/{id}', [InsideC::class, 'cloud'])->name('inside.cloud')->middleware('auth');
Route::post('/inside/cloud/data', [CloudInsideC::class, 'cloudData'])->name('inside.cloud.data')->middleware('auth');
Route::post('/inside/cloud/anexos', [CloudInsideC::class, 'cloudAnexos'])->name('inside.cloud.anexos')->middleware('auth');
Route::post('/inside/cloud/oficios', [CloudInsideC::class, 'cloudOficios'])->name('inside.cloud.oficios')->middleware('auth');
Route::post('/inside/cloud/upload', [CloudInsideC::class, 'upload'])->name('inside.cloud.upload')->middleware('auth');
Route::post('/inside/cloud/delete', [CloudInsideC::class, 'delete'])->name('inside.cloud.delete')->middleware('auth');
Route::get('/inside/generate-pdf/{id}', [ReporteTemplateC::class, 'inside'])->middleware('auth');

//ROUTE ROUND / CIRCULARES
Route::get('/round/list', [RoundC::class, 'list'])->name('round.list')->middleware('auth');
Route::post('/round/table', [RoundC::class, 'table'])->name('round.table')->middleware('auth');
Route::get('/round/create', [RoundC::class, 'create'])->name('round.create')->middleware('auth');
Route::get('/round/edit/{id}', [RoundC::class, 'edit'])->name('round.edit')->middleware('auth');
Route::post('/round/save', [RoundC::class, 'save'])->name('round.save')->middleware('auth');
Route::get('/round/cloud/{id}', [RoundC::class, 'cloud'])->name('round.cloud')->middleware('auth');
Route::post('/round/cloud/data', [CloudRoundC::class, 'cloudData'])->name('round.cloud.data')->middleware('auth');
Route::post('/round/cloud/anexos', [CloudRoundC::class, 'cloudAnexos'])->name('round.cloud.anexos')->middleware('auth');
Route::post('/round/cloud/oficios', [CloudRoundC::class, 'cloudOficios'])->name('round.cloud.oficios')->middleware('auth');
Route::post('/round/cloud/upload', [CloudRoundC::class, 'upload'])->name('round.cloud.upload')->middleware('auth');
Route::post('/round/cloud/delete', [CloudRoundC::class, 'delete'])->name('round.cloud.delete')->middleware('auth');
Route::get('/round/generate-pdf/{id}', [ReporteTemplateC::class, 'round'])->middleware('auth');

//ROUTE file / EXPEDIENTES
Route::get('/file/list', [FileC::class, 'list'])->name('file.list')->middleware('auth');
Route::post('/file/table', [FileC::class, 'table'])->name('file.table')->middleware('auth');
Route::get('/file/create', [FileC::class, 'create'])->name('file.create')->middleware('auth');
Route::get('/file/edit/{id}', [FileC::class, 'edit'])->name('file.edit')->middleware('auth');
Route::post('/file/save', [FileC::class, 'save'])->name('file.save')->middleware('auth');
Route::get('/file/cloud/{id}', [FileC::class, 'cloud'])->name('file.cloud')->middleware('auth');
Route::post('/file/cloud/data', [CloudFileC::class, 'cloudData'])->name('file.cloud.data')->middleware('auth');
Route::post('/file/cloud/anexos', [CloudFileC::class, 'cloudAnexos'])->name('file.cloud.anexos')->middleware('auth');
Route::post('/file/cloud/oficios', [CloudFileC::class, 'cloudOficios'])->name('file.cloud.oficios')->middleware('auth');
Route::post('/file/cloud/upload', [CloudFileC::class, 'upload'])->name('file.cloud.upload')->middleware('auth');
Route::post('/file/cloud/delete', [CloudFileC::class, 'delete'])->name('file.cloud.delete')->middleware('auth');
Route::get('/file/generate-pdf/{id}', [ReporteTemplateC::class, 'file'])->middleware('auth');


/// GLOBAL DE CORRESPONDENCIA
//ALFRESCO -> Descargar archivo
Route::post('/cloud/download', [AlfrescoC::class, 'download'])->name('cloud.download')->middleware('auth');
//ALFRESCO -> ver archivo
Route::post('/cloud/see', [AlfrescoC::class, 'see'])->name('cloud.see')->middleware('auth');
//Collection
Route::post('/year/getYear', [CollectionYearC::class, 'getYear'])->name('year.getYear')->middleware('auth');
// CONSECUTIVO DE AREA ->
ROUTE::post('/collection/area/consecutivo', [CollectionAreaC::class, 'areaAutoincrement'])->middleware('auth');
//GENERACION DE REPORTE
Route::get('/other/generate-pdf/office/{id}', [ReporteCorrespondenciaC::class, 'generatePdf'])->middleware('auth');
// VALIDACION DE NO DE CORRESPONDENCIA
Route::post('/collection/validate/letter', [CollectionAreaC::class, 'getletter'])->middleware('auth');
// TRAE INFORMACION COMO EL NO DE CORRESPONDENCIA QUE EXISTA ASI COMO USUAIRO
Route::post('/valitade/letter', [LetterC::class, 'getletter'])->middleware('auth');