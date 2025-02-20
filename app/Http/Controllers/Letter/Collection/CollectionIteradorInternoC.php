<?php

namespace App\Http\Controllers\Letter\Collection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Letter\Collection\CollectionConsecutivoInternoM;
use App\Models\Letter\Collection\CollectionDateM;

class CollectionIteradorInternoC extends Controller
{
    // La función retorna el nuevo no de oficio, por si ya se le ha asignado a alguien mas
    public function refreshNoOficio()
    {
        // Class
        $collectionConsecutivoInternoM = new CollectionConsecutivoInternoM();
        $collectionDateM = new CollectionDateM();

        // Se asigna el nuevo no de oficio para su asignación
        $result = $collectionConsecutivoInternoM->getMaxConsecutivo(config('custom_config.CP_TABLE_CORRESPONDENCIA_INTERNO'), $collectionDateM->idYear())->iterator;

        // Resultado
        return response()->json([
            'result' => $result,
            'status' => true,
        ]);
    }

    // Actualiza el No de oficio
    public function refreshNoRequerimiento()
    {
        // Class
        $collectionConsecutivoInternoM = new CollectionConsecutivoInternoM();
        $collectionDateM = new CollectionDateM();

        // Se asigna el nuevo no de oficio para su asignación
        $result = $collectionConsecutivoInternoM->noDocumento($collectionDateM->idYear(), config('custom_config.CP_TABLE_REQUERIMRNTOS_INTERNO'));

        // Resultado
        return response()->json([
            'result' => $result,
            'status' => true,
        ]);
    }

    // Actualiza el No de nota informativa
    public function refreshNoInformativo()
    {
        // Class
        $collectionConsecutivoInternoM = new CollectionConsecutivoInternoM();
        $collectionDateM = new CollectionDateM();

        // Se asigna el nuevo no de oficio para su asignación
        $result = $collectionConsecutivoInternoM->noDocumento($collectionDateM->idYear(), config('custom_config.CP_TABLE_NOTAS_INTERNO'));

        // Resultado
        return response()->json([
            'result' => $result,
            'status' => true,
        ]);
    }
}
