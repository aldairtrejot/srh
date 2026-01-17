<?php

namespace App\Http\Controllers\Letter\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Letter\Office\OfficeM;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardOfficeC extends Controller
{
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

    $model = new OfficeM();
    $datos = $model->getReporteFiltrado($area, $status, $year);

    if ($datos->isEmpty()) {
        return response()->json(['error' => 'Sin datos'], 400);
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Estilo del encabezado
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['argb' => Color::COLOR_WHITE]],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF10312B']],
    ];

    // Encabezados personalizados (sin fecha_usuario_captura)
    $encabezadosPersonalizados = [
        'num_turno_sistema'        => 'Turno',
        'asunto_oficio'            => 'Asunto',
        'observaciones_oficio'     => 'Observaciones',
        'num_documento'            => 'No. Documento',
        'folio_gestion'            => 'Folio de Gestión',
        'fecha_captura'            => 'Fecha de Captura',
        'fecha_inicio'             => 'Fecha Inicio',
        'fecha_fin'                => 'Fecha Fin',
        'anio'                     => 'Año',
        'estatus'                  => 'Estatus',
        'tramite'                  => 'Trámite',
        'area_responsable'         => 'Área',
        'unidad_responsable'       => 'Unidad',
        'coordinacion_responsable' => 'Coordinación',
        'responsable_area'         => 'Responsable',
        'enlace_responsable'       => 'Enlace',
        'capturado_por'            => 'Capturado por'
    ];

    // Eliminar columna no deseada antes de procesar
    $columnas = array_keys((array) $datos->first());
    $columnas = array_filter($columnas, fn($col) => $col !== 'fecha_usuario_captura');

    // Crear encabezados
    $colIndex = 1;
    $sheet->setCellValueByColumnAndRow($colIndex, 1, 'No.');
    $sheet->getStyleByColumnAndRow($colIndex, 1)->applyFromArray($headerStyle);
    $sheet->getStyleByColumnAndRow($colIndex, 1)->getAlignment()->setHorizontal('center');
    $sheet->getColumnDimensionByColumn($colIndex)->setAutoSize(true);
    $colIndex++;

    foreach ($columnas as $colNombre) {
        $nombreEncabezado = $encabezadosPersonalizados[$colNombre] ?? strtoupper($colNombre);
        $sheet->setCellValueByColumnAndRow($colIndex, 1, $nombreEncabezado);
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
        $sheet->setCellValueByColumnAndRow($colIndex++, $row, $contador++);
        foreach ($columnas as $colNombre) {
            $valor = $dato->$colNombre;

            // Formatear fechas
            if (in_array($colNombre, ['fecha_captura', 'fecha_inicio', 'fecha_fin']) && $valor) {
                try {
                    $valor = \Carbon\Carbon::parse($valor)->format('d/m/Y');
                } catch (\Exception $e) {
                    // deja el valor original si falla
                }
            }

            $sheet->setCellValueByColumnAndRow($colIndex++, $row, $valor);
        }
        $row++;
    }

    $writer = new Xlsx($spreadsheet);

    return new StreamedResponse(function () use ($writer) {
        if (ob_get_contents()) ob_end_clean();
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment; filename="reporte_oficios.xlsx"',
        'Cache-Control' => 'max-age=0',
        'Pragma' => 'public',
    ]);
}

}