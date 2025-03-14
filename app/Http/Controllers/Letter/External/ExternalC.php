<?php

namespace App\Http\Controllers\Letter\External;
use App\Models\Letter\Collection\CollectionConsecutivoM;
use App\Models\Letter\Collection\CollectionDependenciaM;
use App\Models\Letter\External\ExternalM;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Letter\Collection\CollectionDateM;
class ExternalC extends Controller
{
    //La funcion retorna la vista principal de la tabla
    public function list()
    {
        return view('letter/external/list');
    }

    // La función retorna la tabla de circulares externas
    public function table(Request $request)
    {
        $externalM = new ExternalM();

        $value = $externalM->list($request->iterator, $request->searchValue);

        // Responder con los resultados
        return response()->json([
            'value' => $value,
            'status' => true,
        ]);
    }

    public function create()
    {
        $item = new ExternalM();
        $collectionDependenciaM = new CollectionDependenciaM();
        $collectionConsecutivoM = new CollectionConsecutivoM();
        $collectionDateM = new CollectionDateM();

        $item->fecha_captura = now()->format('d/m/Y'); // Formato de fecha: día/mes/año
        $item->anio = now()->format('Y');
        $item->num_turno_sistema = $collectionConsecutivoM->noDocumento($collectionDateM->idYear(), config('custom_config.CP_TABLE_CIRCULARES_EXT'));

        $selectDependencia = $collectionDependenciaM->list(); //Catalogo de area
        $selectDependenciaEdit = []; //catalogo de area null

        $selectArea = [];//Validacion de id_en DB para definir si se poblan los catalogos o son vacios
        $selectAreaEdit = [];//Validacion de id_en DB para definir si se poblan los catalogos o son vacios

        return view('letter/external/form', compact('selectAreaEdit', 'selectArea', 'selectDependenciaEdit', 'selectDependencia', 'item'));
    }

    // La función retorna el area dependiendo de la dependencia, seleccionada
    public function area(Request $request)
    {
        $collectionDependenciaM = new CollectionDependenciaM();
        $select = $collectionDependenciaM->areaList($request->id);

        return response()->json([
            'collectionArea' => $select,
            'status' => true,
        ]);
    }
}
