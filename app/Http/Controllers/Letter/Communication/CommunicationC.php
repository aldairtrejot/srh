<?php

namespace App\Http\Controllers\Letter\Communication;

use App\Http\Controllers\Controller;
use App\Models\Letter\Communication\CommunicationM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// Hace referencia correspondencia interna
class CommunicationC extends Controller
{
    // Retorna la vista para correspondencia
    public function list()
    {
        return view('letter/communication/list');
    }

    // La función retorna los valores para mostrar la tabla
    public function table(Request $request)
    {
        try {
            // Declaración de variables
            $communicationM = new CommunicationM();
            $iterator = $request->iteradorAux; // OFSET valor de paginador
            $searchValue = $request->searchValue; // Valor de búsqueda
            $value = $communicationM->list($iterator, $searchValue); // Llamamos al método list() con los parámetros necesarios

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
