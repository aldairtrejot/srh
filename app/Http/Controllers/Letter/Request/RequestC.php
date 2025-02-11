<?php

namespace App\Http\Controllers\Letter\Request;

use App\Http\Controllers\Controller;
use App\Models\Letter\Request\RequestM;
use Illuminate\Http\Request;

class RequestC extends Controller
{
    // Retorna la vista para Notas de requerimiento
    public function list()
    {
        return view('letter/request/list');
    }

    // La función retorna los valores para mostrar la tabla
    public function table(Request $request)
    {
        try {
            // Declaración de variables
            $requestM = new RequestM();
            $iterator = $request->iterator; // OFSET valor de paginador
            $searchValue = $request->searchValue; // Valor de búsqueda
            $value = $requestM->list($iterator, $searchValue); // Llamamos al método list() con los parámetros necesarios

            return response()->json([
                'value' => $value,
                'status' => true,
            ]);

        } catch (\Exception $e) {
            // Manejo de errores en caso de excepciones
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


}
