<?php
use App\Http\Controllers\Administration\LoginC;
use App\Http\Controllers\Administration\RecoverC;
use App\Http\Controllers\Administration\RegisterC;
use App\Http\Controllers\Administration\ResultC;

// NO AUTHENTICATION REQUIRED
Route::get('/register', RegisterC::class)->name('register'); ///ROUTE_REGISTER
Route::get('/recover', RecoverC::class)->name('recover');//ROUTE_RECOVER
Route::get('/login', LoginC::class)->name('login'); //ROUTE_LOGIN
Route::post('/login', [LoginC::class, 'authenticate']); //ROUTE_AUTHENTICATE
Route::post('/password/result', [RecoverC::class, 'updatePassword'])->name('recover.password'); // RECOVER_PASSWORD
Route::get('/result', [ResultC::class, 'result'])->name('result'); //PASSWORD_RESULT

//AUTHENTICATION REQUIRED
Route::post('/logout', [LoginC::class, 'logout'])->name('logout')->middleware('auth'); //ROUTE_LOGOUT