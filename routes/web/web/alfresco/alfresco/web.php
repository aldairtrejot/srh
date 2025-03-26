<?php

use App\Http\Controllers\Cloud\AlfrescoC;


Route::post('/cloud/download', [AlfrescoC::class, 'download'])->name('cloud.download')->middleware('auth'); //ALFRESCO -> Descargar archivo
Route::post('/cloud/see', [AlfrescoC::class, 'see'])->name('cloud.see')->middleware('auth'); //ALFRESCO -> ver archivo




