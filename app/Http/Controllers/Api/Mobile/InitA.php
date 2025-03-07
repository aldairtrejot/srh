<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InitA extends Controller
{
    public function getData()
    {
        return response()->json(['message' => 'Hola, este es un mensaje en JSON']);
    }
}
