<?php

namespace App\Http\Controllers\Letter\External;
use App\Models\Letter\Collection\CollectionConsecutivoM;
use App\Models\Letter\Collection\CollectionDependenciaM;
use App\Models\Letter\External\ExternalM;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Letter\Collection\CollectionDateM;
use Carbon\Carbon;
use App\Http\Controllers\Admin\MessagesC;
use App\Http\Controllers\Letter\Log\LogC;
use Illuminate\Support\Facades\Auth;
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

    public function edit($id)
    {
        $externalM = new ExternalM();
        $collectionDependenciaM = new CollectionDependenciaM();

        $item = $externalM->edit($id);
        $item->anio = date("Y", strtotime($item->fecha_captura));
        $item->fecha_captura = date("d/m/Y", strtotime($item->fecha_captura)); // Formato de fecha: día/mes/año

        $selectDependencia = $collectionDependenciaM->list(); //Catalogo de area
        $selectDependenciaEdit = isset($item->id_cat_dependencia) ? $collectionDependenciaM->listEdit($item->id_cat_dependencia) : [];

        $selectArea = isset($item->id_cat_dependencia) ? $collectionDependenciaM->areaList($item->id_cat_dependencia) : [];
        $selectAreaEdit = isset($item->id_cat_dependencia) && isset($item->id_cat_dependencia_area) ? $collectionDependenciaM->listAreaEdit($item->id_cat_dependencia_area) : [];

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

    // La función valida que sea unico registro
    public function unique(Request $request)
    {
        $externalM = new ExternalM();
        $status = $externalM->unique($request->id, $request->no_documento);
        return response()->json([
            'status' => $status,
        ]);
    }

    public function save(Request $request)
    {
        $logC = new LogC();
        $messagesC = new MessagesC();
        $collectionConsecutivoM = new CollectionConsecutivoM();
        $now = Carbon::now(); //Hora y fecha actual
        $collectionDateM = new CollectionDateM();
        $externalM = new ExternalM();

        if (!isset($request->id_tbl_circular_externa)) {
            //Agregar elementos
            $data = [
                'num_turno_sistema' => $collectionConsecutivoM->noDocumento($collectionDateM->idYear(), config('custom_config.CP_TABLE_CIRCULARES_EXT')),
                'no_documento' => strtoupper($request->no_documento),
                'fecha_captura' => now()->format('Y-m-d'),
                'fecha_documento' => $request->fecha_documento, 
                'asunto' => strtoupper($request->asunto),
                'observaciones' => strtoupper($request->observaciones),
                'id_cat_dependencia' => $request->id_cat_dependencia,
                'id_cat_dependencia_area' => $request->id_cat_dependencia_area,

                // DATA_SYSTEM
                'id_usuario_captura' => Auth::user()->id,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario_captura' => $now,
                'fecha_usuario' => $now,
            ];

            $externalM::create($data);
            $logC->add('correspondencia.tbl_circular_externa', $data);

            //se itera el consevutivo
            $collectionConsecutivoM->iteratorConsecutivo($collectionDateM->idYear(), config('custom_config.CP_TABLE_CIRCULARES_EXT'));

            return $messagesC->messageSuccessRedirect('external.list', 'Elemento agregado con éxito.');

        } else { //modificar elemento 

            $data = [
                'no_documento' => strtoupper($request->no_documento),
                'fecha_documento' => $request->fecha_documento,
                'asunto' => strtoupper($request->asunto),
                'observaciones' => strtoupper($request->observaciones),
                'id_cat_dependencia' => $request->id_cat_dependencia,
                'id_cat_dependencia_area' => $request->id_cat_dependencia_area,

                // DATA_SYSTEM
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];

            $externalM::where('id_tbl_circular_externa', $request->id_tbl_circular_externa)
                ->update($data);
            $data['id_tbl_circular_externa'] = $request->id_tbl_circular_externa;
            $logC->edit('correspondencia.tbl_circular_externa', $data);


            return $messagesC->messageSuccessRedirect('external.list', 'Elemento modificado con éxito.');
        }
    }
}
