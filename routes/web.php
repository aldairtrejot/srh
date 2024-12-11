<?php

use App\Http\Controllers\Letter\Collection\CollectionYearC;
use App\Http\Controllers\Letter\Office\CloudC;
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


//Collection
Route::post('/year/getYear', [CollectionYearC::class, 'getYear'])->name('year.getYear')->middleware('auth');
