<?php

use App\Http\Controllers\Letter\Communication\CommunicationC;
use App\Http\Controllers\Letter\Collection\CollectionAreaInternoC;

// GET
Route::get('/communication/edit/{id}', [CommunicationC::class, 'edit'])->name('communication.edit')->middleware('auth');
Route::get('/communication', [CommunicationC::class, 'list'])->name('communication.list')->middleware('auth');
Route::get('/communication/create', [CommunicationC::class, 'create'])->name('communication.create')->middleware('auth');

// POST
Route::post('/communication/save', [CommunicationC::class, 'save'])->name('communication.save')->middleware('auth');
Route::post('/communication/updateOficio', [CommunicationC::class, 'updateOficio'])->name('communication.updateOficio')->middleware('auth');
Route::post('/communication/updateAcuse', [CommunicationC::class, 'updateAcuse'])->name('communication.updateAcuse')->middleware('auth');
Route::post('/communication/addOficio', [CommunicationC::class, 'addOficio'])->name('communication.addOficio')->middleware('auth');
Route::post('/communication/addAcuse', [CommunicationC::class, 'addAcuse'])->name('communication.addAcuse')->middleware('auth');
Route::post('/communication/table', [CommunicationC::class, 'table'])->name('communication.table')->middleware('auth');
Route::post('/communication/area', [CollectionAreaInternoC::class, 'list'])->name('communication.area')->middleware('auth');