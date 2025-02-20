<?php

namespace App\Http\Controllers\Letter\Collection;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Letter\Collection\CollectionSolicitanteM;
use Illuminate\Http\Request;
use App\Http\Controllers\Letter\Log\LogC;
use Carbon\Carbon;

class CollectionSolicitanteC extends Controller
{
    // La función valida o agrega un nuevo solicitante
    public function addSolcitante(Request $request)
    {
        // Class
        $collectionSolicitanteM = new CollectionSolicitanteM();
        $logC = new LogC();
        $now = Carbon::now(); //Hora y fecha actual

        // Value
        $estatus = $collectionSolicitanteM->equalName($request->name); // Valida que el nombre no exista
        $solicitanteAll = $collectionSolicitanteM->list(); // Catalogo global de solicitantes
        $solicitanteEdit = null;

        if (!$estatus) {

            $data = [ // Array con valores
                'nombre' => strtoupper($request->name),
                'primer_apellido' => strtoupper($request->firstName),
                'segundo_apellido' => strtoupper($request->seconName),
                'estatus' => true,
                'id_usuario_sistema' => Auth::user()->id, //DATA_SYSTEM
                'fecha_usuario' => $now, //DATA_SYSTEM
            ];

            $collectionSolicitanteM::create($data); // Se guardan los valores de solicitante
            $logC->add('correspondencia.cat_solicitante', $data); // Se guarda la informacion en bitacora

            $solicitanteEdit = $collectionSolicitanteM->editByName($request->name);
        }

        return response()->json([
            'estatus' => $estatus,
            'solicitanteAll' => $solicitanteAll,
            'solicitanteEdit' => $solicitanteEdit,
        ]);
    }
}
