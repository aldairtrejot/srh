<?php

namespace App\Http\Controllers\Letter\Communication;

use App\Models\Letter\Collection\CollectionAreaInternoM;
use App\Models\Letter\Collection\CollectionConsecutivoInternoM;
use App\Models\Letter\Collection\CollectionDateM;
use App\Models\Letter\Collection\CollectionDestinatarioM;
use App\Models\Letter\Collection\CollectionEntidadM;
use App\Http\Controllers\Controller;
use App\Models\Letter\Collection\CollectionSolicitanteM;
use App\Models\Letter\Collection\CollectionTemaM;
use App\Models\Letter\Communication\CommunicationM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\Log;
// Hace referencia correspondencia interna
class CommunicationC extends Controller
{
    // Retorna la vista para correspondencia
    public function list()
    {
        return view('letter/communication/list');
    }

    // Retorna la vista de create
    public function create()
    {
        // Class
        $item = new CommunicationM();
        $collectionEntidadM = new CollectionEntidadM();
        $collectionDateM = new CollectionDateM();
        $collectionConsecutivoInternoM = new CollectionConsecutivoInternoM();
        $collectionTemaM = new CollectionTemaM();
        $collectionSolicitanteM = new CollectionSolicitanteM();
        $collectionAreaInternoM = new CollectionAreaInternoM();
        $collectionDestinatarioM = new CollectionDestinatarioM();

        //Definicion de variable de inicializacion
        $item->fecha_captura = now()->format('d/m/Y'); // Formato de fecha: día/mes/año
        $nameUser = Auth::user()->name; // Nombre de usuario
        $nomArea = ' _'; // Inicio de variables
        $item->consecutivo = $collectionConsecutivoInternoM->getMaxConsecutivo(config('custom_config.CP_TABLE_CORRESPONDENCIA_INTERNO'), $collectionDateM->idYear())->iterator;

        // Declaración de catalogos
        $selectEntidad = $collectionEntidadM->list();
        $selectEntidadEdit = [];

        $selectTema = $collectionTemaM->list();
        $selectTemaEdit = [];

        $selectSolicitante = $collectionSolicitanteM->list();
        $selectSolicitanteEdit = [];

        $selectArea = $collectionAreaInternoM->list();
        $selectAreaEdit = [];

        $selectDestinatario = $collectionDestinatarioM->list();
        $selectDestinatarioEdit = [];


        return view('letter/communication/form', compact('selectDestinatarioEdit', 'selectDestinatario', 'selectAreaEdit', 'selectArea', 'selectSolicitanteEdit', 'selectSolicitante', 'selectTemaEdit', 'selectTema', 'nomArea', 'nameUser', 'selectEntidadEdit', 'selectEntidad', 'item'));
    }

    // La función retorna los valores para mostrar la tabla
    public function table(Request $request)
    {
        try {
            // Declaración de variables
            $communicationM = new CommunicationM();
            $iterator = $request->iterator; // OFSET valor de paginador
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
