<?php

// AdministrationC.php
namespace App\Http\Controllers\Administration\AdministrationC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdministrationC extends Controller
{
    public function __invoke()
    {
        return view('administration.administrationC.dashboard');
    }
    
}

