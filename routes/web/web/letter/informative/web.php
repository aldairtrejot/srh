<?php

use App\Http\Controllers\Letter\Informative\InformativeC;

// INFORMATIVE
// GET
Route::get('/informative', [InformativeC::class, 'list'])->name('informative.list')->middleware('auth');
Route::get('/informative/create', [InformativeC::class, 'create'])->name('informative.create')->middleware('auth');
Route::get('/informative/edit/{id}', [InformativeC::class, 'edit'])->name('informative.edit')->middleware('auth');

// POST
Route::post('/informative/save', [InformativeC::class, 'save'])->name('informative.save')->middleware('auth');
Route::post('/informative/table', [InformativeC::class, 'table'])->name('informative.table')->middleware('auth');
Route::post('/informative/saveFile', [InformativeC::class, 'saveFile'])->name('informative.saveFile')->middleware('auth');
Route::post('/informative/deleteFile', [InformativeC::class, 'deleteFile'])->name('informative.deleteFile')->middleware('auth');




