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
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function obtenerCatalogos()
{
    $areas = DB::table('correspondencia.cat_dependencia_area')
        ->select('id_cat_dependencia_area', 'descripcion')
        ->orderBy('descripcion')
        ->get();

    $anios = DB::table('correspondencia.tbl_circular_externa')
        ->select(DB::raw("EXTRACT(YEAR FROM fecha_documento)::TEXT AS descripcion"))
        ->groupBy(DB::raw("EXTRACT(YEAR FROM fecha_documento)"))
        ->orderByDesc(DB::raw("EXTRACT(YEAR FROM fecha_documento)"))
        ->get();

    return response()->json([
        'areas' => $areas,
        'anios' => $anios,
    ]);
}

public function descargarReporte(Request $request)
{
    $area = $request->input('area');
    $year = $request->input('year');

    $model = new ExternalM();
    $datos = $model->getReporteEncabezados($area, $year);

    if ($datos->isEmpty()) {
        return response()->json(['error' => 'Sin datos'], 400);
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Estilo para encabezados
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['argb' => Color::COLOR_WHITE],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FF10312B'],
        ],
    ];

    // Mapeo personalizado
    $encabezadosPersonalizados = [
        'no_turno'        => 'No. Turno',
        'fecha_captura'   => 'Fecha de Captura',
        'anio'            => 'Año',
        'dependencia'     => 'Dependencia',
        'area'            => 'Área',
        'fecha_documento' => 'Fecha del Documento',
        'no_documento'    => 'No. Documento',
        'asunto'          => 'Asunto',
        'observaciones'   => 'Observaciones',
    ];

    $columnas = array_keys((array)$datos->first());

    // Encabezados con columna "No." centrada y autoajustada
    $colIndex = 1;
    $sheet->setCellValueByColumnAndRow($colIndex, 1, 'No.');
    $sheet->getStyleByColumnAndRow($colIndex, 1)->applyFromArray($headerStyle);
    $sheet->getStyleByColumnAndRow($colIndex, 1)->getAlignment()->setHorizontal('center');
    $sheet->getColumnDimensionByColumn($colIndex)->setAutoSize(true);
    $colIndex++;

    foreach ($columnas as $colNombre) {
        $etiqueta = $encabezadosPersonalizados[$colNombre] ?? strtoupper($colNombre);
        $sheet->setCellValueByColumnAndRow($colIndex, 1, $etiqueta);
        $sheet->getStyleByColumnAndRow($colIndex, 1)->applyFromArray($headerStyle);
        $sheet->getStyleByColumnAndRow($colIndex, 1)->getAlignment()->setHorizontal('center');
        $sheet->getColumnDimensionByColumn($colIndex)->setAutoSize(true);
        $colIndex++;
    }

    // Llenar los datos
    $row = 2;
    $contador = 1;
    foreach ($datos as $dato) {
        $colIndex = 1;
        $sheet->setCellValueByColumnAndRow($colIndex, $row, $contador);
        $colIndex++;

        foreach ($columnas as $colNombre) {
            $sheet->setCellValueByColumnAndRow($colIndex, $row, $dato->$colNombre);
            $colIndex++;
        }

        $contador++;
        $row++;
    }

    $writer = new Xlsx($spreadsheet);

    return new StreamedResponse(function () use ($writer) {
        if (ob_get_contents()) ob_end_clean();
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment; filename="reporte_circulares_externas.xlsx"',
        'Cache-Control' => 'max-age=0',
        'Pragma' => 'public',
    ]);
}

}
