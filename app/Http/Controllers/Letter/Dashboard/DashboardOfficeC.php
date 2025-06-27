<?php

namespace App\Http\Controllers\Letter\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Letter\Office\OfficeM;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Symfony\Component\HttpFoundation\StreamedResponse;



class DashboardOfficeC extends Controller
{
  
    private function addHeader($sheet, $cell, $value)
    {
        $sheet->setCellValue($cell, $value);
        $style = $sheet->getStyle($cell);
        $style->getFont()->setBold(true);
        $style->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        $style->getFill()->setFillType(Fill::FILL_SOLID);
        $style->getFill()->getStartColor()->setARGB('FF006800'); // VERDE
    }

   public function descargarReporte()
{
    $model = new OfficeM();
    $datos = $model->getReporteOficios();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Validar que hay datos
    if ($datos->isEmpty()) {
        return response()->json(['error' => 'Sin datos'], 400);
    }

    // Estilo para encabezado
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['argb' => Color::COLOR_WHITE],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FF006800'], // Verde oscuro
        ],
    ];

    $columnas = array_keys((array) $datos->first());

    // Crear encabezado
    foreach ($columnas as $colIndex => $colNombre) {
        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
        $sheet->setCellValue("{$colLetter}1", strtoupper($colNombre));
        $sheet->getStyle("{$colLetter}1")->applyFromArray($headerStyle);
    }

    // Agregar datos
    $row = 2;
    foreach ($datos as $dato) {
        foreach ($columnas as $colIndex => $colNombre) {
            $sheet->setCellValueByColumnAndRow($colIndex + 1, $row, $dato->$colNombre);
        }
        $row++;
    }

    // Configurar escritor
    $writer = new Xlsx($spreadsheet);

    // Forzar headers limpios y sin espacios en blanco
    return new StreamedResponse(function () use ($writer) {
        // Limpiar cualquier posible salida previa
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
