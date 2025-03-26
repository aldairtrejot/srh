<?php

use App\Http\Controllers\Letter\Certification\CertificationC;

// GET
Route::get('/certification/list', [CertificationC::class, 'list'])->name('certification.list')->middleware('auth');

// POST
Route::post('/certification/table', [CertificationC::class, 'table'])->name('certification.table')->middleware('auth');
Route::post('/certification/saveFile', [CertificationC::class, 'saveFile'])->name('certification.saveFile')->middleware('auth');
Route::post('/certification/deleteFile', [CertificationC::class, 'deleteFile'])->name('certification.deleteFile')->middleware('auth');

