<?php

namespace App\Http\Controllers\Letter\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Letter\Office\OfficeM;
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

    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['argb' => Color::COLOR_WHITE],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FF006800'],
        ],
    ];

    $columnas = array_keys((array)$datos->first());

    // Generar encabezados
    $colIndex = 1;
    foreach ($columnas as $colNombre) {
        if ($colNombre === 'fecha_usuario_captura') {
            $sheet->setCellValueByColumnAndRow($colIndex, 1, 'FECHA CAPTURA');
            $sheet->getStyleByColumnAndRow($colIndex, 1)->applyFromArray($headerStyle);
            $colIndex++;
            $sheet->setCellValueByColumnAndRow($colIndex, 1, 'HORA CAPTURA');
            $sheet->getStyleByColumnAndRow($colIndex, 1)->applyFromArray($headerStyle);
        } else {
            $sheet->setCellValueByColumnAndRow($colIndex, 1, strtoupper($colNombre));
            $sheet->getStyleByColumnAndRow($colIndex, 1)->applyFromArray($headerStyle);
        }
        $colIndex++;
    }

    // Llenar datos
    $row = 2;
    foreach ($datos as $dato) {
        $colIndex = 1;
        foreach ($columnas as $colNombre) {
            if ($colNombre === 'fecha_usuario_captura') {
                if ($dato->$colNombre) {
                    $fechaHora = explode(' ', $dato->$colNombre);
                    $fecha = $fechaHora[0] ?? '';
                    $hora = $fechaHora[1] ?? '';
                } else {
                    $fecha = '';
                    $hora = '';
                }
                $sheet->setCellValueByColumnAndRow($colIndex, $row, $fecha);
                $colIndex++;
                $sheet->setCellValueByColumnAndRow($colIndex, $row, $hora);
            } else {
                $sheet->setCellValueByColumnAndRow($colIndex, $row, $dato->$colNombre);
            }
            $colIndex++;
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
