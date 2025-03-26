<?php

use App\Http\Controllers\Letter\Collection\CollectionIteradorInternoC;
use App\Http\Controllers\Letter\Collection\CollectionSolicitanteC;
use App\Http\Controllers\Letter\Collection\CollectionYearC;

// GET


// POST
Route::post('/communication/noOficio', [CollectionIteradorInternoC::class, 'refreshNoOficio'])->name('communication.noOficio')->middleware('auth');
Route::post('/request/noOficio', [CollectionIteradorInternoC::class, 'refreshNoRequerimiento'])->name('request.noOficio')->middleware('auth');
Route::post('/informative/noOficio', [CollectionIteradorInternoC::class, 'refreshNoInformativo'])->name('informative.noOficio')->middleware('auth');
Route::post('/solicitante/add', [CollectionSolicitanteC::class, 'addSolcitante'])->name('solicitante.add')->middleware('auth');
Route::post('/year/getYear', [CollectionYearC::class, 'getYear'])->name('year.getYear')->middleware('auth');
