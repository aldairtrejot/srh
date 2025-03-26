<?php

use Mews\Captcha\Facades\Captcha;

// CONFIG_CAPTCHA
Route::get('captcha/{config?}', function ($config = 'default') {
    return Captcha::create($config);
})->name('captcha');