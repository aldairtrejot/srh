<?php

use App\Http\Controllers\Email\EmailC;

// ENVIO DE CORREO ELECTRONICO
// GET

// POST
Route::post('/letter/email', [EmailC::class, 'emailLetter'])->middleware('auth');