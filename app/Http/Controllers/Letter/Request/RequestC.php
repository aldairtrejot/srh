<?php

namespace App\Http\Controllers\Letter\Request;

use App\Models\Letter\Collection\CollectionConsecutivoInternoM;
use App\Models\Letter\Collection\CollectionSolicitanteM;
use App\Http\Controllers\Controller;
use App\Models\Letter\Request\RequestM;
use Illuminate\Http\Request;
use App\Models\Letter\Collection\CollectionDateM;

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

    public function create()
    {
        // Class
        $item = new RequestM();
        $collectionDateM = new CollectionDateM();
        $collectionConsecutivoInternoM = new CollectionConsecutivoInternoM();
        $collectionSolicitanteM = new CollectionSolicitanteM();

        //Definicion de variable de inicializacion
        $item->fecha_asignacion = now()->format('d/m/Y'); // Formato de fecha: día/mes/año
        $item->consecutivo = $collectionConsecutivoInternoM->noDocumento($collectionDateM->idYear(), config('custom_config.CP_TABLE_REQUERIMRNTOS_INTERNO'));

        // Declaración de catalogos
        $selectSolicitante = $collectionSolicitanteM->list();
        $selectSolicitanteEdit = [];

        return view('letter/request/form', compact('selectSolicitanteEdit', 'selectSolicitante', 'item'));
    }

}
