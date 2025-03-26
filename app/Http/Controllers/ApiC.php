<?php

namespace App\Http\Controllers;
// Test conexion API
class ApiC extends Controller
{
    // La función retorna un status de éxito si es que existe conexión con la API
    public function status()
    {
        return response()->json([
            'message' => 'API WORKING CORRECTLY',
            'status' => true,
        ], 200); // code 200 ok
    }
}
