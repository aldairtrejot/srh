<?php

namespace App\Http\Controllers\Letter\Round;
use App\Models\Letter\Letter\LetterM;
use App\Models\Letter\Round\RoundM;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Letter\Collection\CollectionDateM;
use App\Models\Letter\Collection\CollectionConsecutivoM;
use App\Models\Letter\Collection\CollectionAreaM;
use App\Models\Letter\Collection\CollectionRemitenteM;
use App\Models\Letter\Collection\CollectionRelEnlaceM;
use App\Models\Letter\Collection\CollectionRelUsuarioM;
use Carbon\Carbon;
use App\Http\Controllers\Admin\MessagesC;
use App\Models\Letter\Collection\CollectionReportM;
use App\Http\Controllers\Letter\Log\LogC;
use App\Http\Controllers\Letter\Other\ConsecutivoC;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RoundC extends Controller
{
    //La funcion retorna la vista principal de la tabla
    public function list()
    {
        return view('letter/round/list');
    }

    public function cloud($id)
    {
        $collectionReportM = new CollectionReportM();
        $object = new RoundM();
        $item = $object->edit($id);
        $id_cat_area = $collectionReportM->getIdArea($id, 'correspondencia.tbl_circular', 'id_tbl_circular');
        return view('letter/round/cloud', compact('id_cat_area', 'item', 'id'));

    }

    //La funcion crea ta tabla dependiedp de los roles que se han ingreado
    public function table(Request $request)
    {
        try {
            $model = new RoundM();
            // Obtener valores de la solicitud
            $iterator = $request->input('iterator'); // OFSET valor de paginador
            $searchValue = $request->input('searchValue'); // Valor de búsqueda
            $roleUserArray = collect(session('SESSION_ROLE_USER'))->toArray(); // Array con roles de usuario
            $ADM_TOTAL = config('custom_config.ADM_TOTAL'); // Acceso completo
            $COR_TOTAL = config('custom_config.COR_TOTAL'); // Acceso completo a correspondencia
            $COR_USUARIO = config('custom_config.COR_USUARIO'); // Acceso por área

            // Verificar si el usuario tiene acceso completo
            if (in_array($ADM_TOTAL, $roleUserArray) || in_array($COR_TOTAL, $roleUserArray)) {
                // Si tiene acceso completo, no hay necesidad de filtrar por área o enlace
                // Procesar la tabla con acceso completo si es necesario
                $value = $model->list($iterator, $searchValue, null);
            } else {
                // Llamamos al método list() con los parámetros necesarios
                $value = $model->list($iterator, $searchValue, Auth::id());
            }

            // Responder con los resultados
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
        $item = new RoundM();
        $collectionDateM = new CollectionDateM();
        $collectionConsecutivoM = new CollectionConsecutivoM();
        $collectionAreaM = new CollectionAreaM();
        $collectionRemitenteM = new CollectionRemitenteM();

        $item->fecha_captura = now()->format('d/m/Y'); // Formato de fecha: día/mes/año
        $item->id_cat_anio = $collectionDateM->idYear();
        $item->num_turno_sistema = $collectionConsecutivoM->noDocumento($item->id_cat_anio, config('custom_config.CP_TABLE_CIRCULAR'));
        $item->es_por_area = true; //Iniciamos la variable en falso para asociar con el nuevo no de documento

        $noLetter = "";//No de oficio se inicializa en vacio

        $selectAreaAux = $collectionAreaM->list(); //Catalogo de area
        $selectAreaEditAux = []; //catalogo de area null

        $selectUser = [];//Validacion de id_en DB para definir si se poblan los catalogos o son vacios
        $selectUserEdit = [];//Validacion de id_en DB para definir si se poblan los catalogos o son vacios

        $selectEnlace = [];//Validacion de id_en DB para definir si se poblan los catalogos o son vaciosvacios
        $selectEnlaceEdit = [];////Validacion de id_en DB para definir si se poblan los catalogos o son vaciosvacios

        return view('letter/round/form', compact('selectEnlaceEdit', 'selectEnlace', 'selectUserEdit', 'selectUser', 'selectAreaEditAux', 'selectAreaAux', 'noLetter', 'item'));
    }

    public function edit(string $id)
    {
        $object = new RoundM();
        $collectionAreaM = new CollectionAreaM();
        $collectionRelUsuarioM = new CollectionRelUsuarioM();
        $collectionRelEnlaceM = new CollectionRelEnlaceM();
        $collectionRemitenteM = new CollectionRemitenteM();
        $letterM = new LetterM();

        $item = $object->edit($id); // Obtener el elemento con el ID pasado
        $noLetter = $letterM->getTurno($item->id_tbl_correspondencia);

        $selectAreaAux = $collectionAreaM->list(); //Catalogo de area
        $selectAreaEditAux = isset($item->id_cat_area_documento) ? $collectionAreaM->edit($item->id_cat_area_documento) : []; //catalogo de area null

        $selectUser = isset($item->id_cat_area) ? $collectionRelUsuarioM->idUsuarioByArea($item->id_cat_area) : [];//Validacion de id_en DB para definir si se poblan los catalogos o son vacios
        $selectUserEdit = isset($item->id_cat_area) && isset($item->id_usuario_area) ? $collectionRelUsuarioM->idUsuarioByAreaEdit($item->id_usuario_area) : [];//Validacion de id_en DB para definir si se poblan los catalogos o son vacios

        $selectEnlace = isset($item->id_cat_area) ? $collectionRelEnlaceM->idUsuarioByArea($item->id_cat_area) : [];//Validacion de id_en DB para definir si se poblan los catalogos o son vaciosvacios
        $selectEnlaceEdit = isset($item->id_cat_area) && isset($item->id_usuario_enlace) ? $collectionRelUsuarioM->idUsuarioByAreaEdit($item->id_usuario_enlace) : [];////Validacion de id_en DB para definir si se poblan los catalogos o son vaciosvacios

        return view('letter/round/form', compact('selectEnlaceEdit', 'selectEnlace', 'selectUserEdit', 'selectUser', 'selectAreaEditAux', 'selectAreaAux', 'noLetter', 'item'));
    }

    public function save(Request $request)
    {
        $logC = new LogC();
        $object = new RoundM();
        $messagesC = new MessagesC();
        $collectionConsecutivoM = new CollectionConsecutivoM();
        $letterM = new LetterM();
        $collectionAreaM = new CollectionAreaM();
        $consecutivoC = new ConsecutivoC();

        $now = Carbon::now(); //Hora y fecha actual
        $es_por_area = isset($request->es_por_area) ? 1 : 0; //Se condiciona el valor del check

        if (!isset($request->id_tbl_circular)) { // || empty($request->id_tbl_correspondencia)) { // Creación de nuevo nuevo elemento
            //Agregar elementos
            //Agregar elementos
            if ($this->getMaxTurno($request->num_turno_sistema) <= $object->getMaxNuSistem()) {
                $numTurnoSistemaAux = $this->procesarParametros($request->num_turno_sistema, $collectionConsecutivoM->noDocumento($request->id_cat_anio, config('custom_config.CP_TABLE_CIRCULAR')));
                //$collectionConsecutivoM->iteratorConsecutivo($request->id_cat_anio, config('custom_config.CP_TABLE_CORRESPONDENCIA'));
            } else {
                $numTurnoSistemaAux = $request->num_turno_sistema;
            }

            // Validacion de es por area sea unico, de lo contrario se concatena la la variable
            $noDocumentoAreaAux = $request->num_documento_area;
            if ($es_por_area == 1) {
                if ($consecutivoC->getOnlyNo($request->num_documento_area) <= $object->getOnly($request->id_cat_area_documento, $request->id_cat_anio)->max_num) {
                    $noDocumentoAreaAux = $consecutivoC->setNoConsecutivo($request->num_documento_area, $collectionAreaM->noDocumentoByAux($request->id_cat_anio, $request->id_cat_area_documento, 'correspondencia.rel_consecutivo_circulares'));
                }
            }

            $data = [
                'num_turno_sistema' => strtoupper($numTurnoSistemaAux),
                'fecha_captura' => Carbon::createFromFormat('d/m/Y', $request->fecha_captura)->format('Y-m-d'),
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'asunto' => strtoupper($request->asunto),
                'observaciones' => strtoupper($request->observaciones),
                'id_tbl_correspondencia' => $request->id_tbl_correspondencia,
                'id_cat_anio' => $request->id_cat_anio,
                'es_por_area' => $es_por_area,
                'num_documento_area' => strtoupper($noDocumentoAreaAux),
                'id_cat_area_documento' => $request->id_cat_area_documento,
                'id_usuario_area' => $request->id_usuario_area,
                'id_usuario_enlace' => $request->id_usuario_enlace,
                'id_cat_area' => $request->id_cat_area_documento,
                'destinatario' => strtoupper($request->destinatario),

                // DATA_SYSTEM
                'id_usuario_sistema' => Auth::user()->id,
                'id_usuario_captura' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];

            $object::create($data);
            $logC->add('correspondencia.tbl_circular', $data);

            //se itera el consevutivo
            $collectionConsecutivoM->iteratorConsecutivo($request->id_cat_anio, config('custom_config.CP_TABLE_CIRCULAR'));
            $collectionAreaM->iteratorConsecutivoAux($request->id_cat_anio, $request->id_cat_area_documento, 'correspondencia.rel_consecutivo_circulares');

            return $messagesC->messageSuccessRedirect('round.list', 'Elemento agregado con éxito.');

        } else { //modificar elemento 

            $data = [
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'asunto' => strtoupper($request->asunto),
                'observaciones' => strtoupper($request->observaciones),
                'id_tbl_correspondencia' => $request->id_tbl_correspondencia,
                'es_por_area' => $es_por_area,
                'num_documento_area' => $request->num_documento_area,
                'id_cat_area_documento' => $request->id_cat_area_documento,
                'id_usuario_area' => $request->id_usuario_area,
                'id_usuario_enlace' => $request->id_usuario_enlace,
                'id_cat_area' => $request->id_cat_area_documento,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
                'destinatario' => strtoupper($request->destinatario),
            ];

            $object::where('id_tbl_circular', $request->id_tbl_circular)
                ->update($data);
            $data['id_tbl_circular'] = $request->id_tbl_circular;
            $logC->edit('correspondencia.tbl_circular', $data);

            return $messagesC->messageSuccessRedirect('round.list', 'Elemento modificado con éxito.');

        }
    }

    // la funcion elimina los espacios para obtener solo los numero de / ***(
    private function getMaxTurno($numTurno)
    {
        // Usamos una expresión regular para capturar los 5 dígitos entre las barras "/"
        if (preg_match('/\/([0-9]{5})\//', $numTurno, $matches)) {
            // $matches[1] contiene los 5 dígitos capturados
            return (int) $matches[1]; // Devolvemos el número como entero
        }

        return null; // Si no encuentra el patrón, devolvemos null
    }

    private function procesarParametros($param1, $param2)
    {
        // Extraemos la parte antes del primer '/'
        preg_match('/^([A-Za-z]+)/', $param1, $coincidencias1);
        $letras1 = $coincidencias1[1];

        // Extraemos la parte entre los '/' de param2
        preg_match('/\/(\d+)\//', $param2, $coincidencias2);
        $numeros2 = $coincidencias2[1];

        // Concatenamos las partes
        return $letras1 . '/' . $numeros2 . '/2025';
    }

   public function obtenerCatalogos()
    {
        $areas = DB::table('correspondencia.cat_area')
            ->select('id_cat_area', 'descripcion')
            ->orderBy('descripcion')
            ->get();

        $estatus = DB::table('correspondencia.cat_estatus')
            ->select('id_cat_estatus', 'descripcion')
            ->orderBy('descripcion')
            ->get();

        $anios = DB::table('correspondencia.cat_anio')
            ->select('id_cat_anio', 'descripcion')
            ->orderBy('descripcion')
            ->get();

        return response()->json([
            'areas' => $areas,
            'estatus' => $estatus,
            'anios' => $anios,
        ]);
    }

    public function descargarReporte(Request $request)
    {
        $area = $request->input('area');
        $status = $request->input('status');
        $year = $request->input('year');

        $model = new RoundM();
        $datos = $model->getReporteFiltrado($area, $status, $year);

        if ($datos->isEmpty()) {
            return response()->json(['error' => 'Sin datos'], 400);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF006800'],
            ],
        ];

        $columnas = array_keys((array)$datos->first());

        foreach ($columnas as $i => $col) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($colLetter . '1', strtoupper($col));
            $sheet->getStyle($colLetter . '1')->applyFromArray($headerStyle);
        }

        $row = 2;
        foreach ($datos as $dato) {
            foreach ($columnas as $i => $col) {
                $sheet->setCellValueByColumnAndRow($i + 1, $row, $dato->$col);
            }
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            if (ob_get_contents()) ob_end_clean();
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="reporte_interno.xlsx"',
            'Cache-Control' => 'max-age=0',
            'Pragma' => 'public',
        ]);
    }
}