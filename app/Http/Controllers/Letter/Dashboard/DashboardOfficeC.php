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
    public function generate()
    {
        ob_end_clean(); // Elimina cualquier salida previa

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $headers = [
            'A1' => 'N° Turno Sistema',
            'B1' => 'Documento',
            'C1' => 'Asunto',
            'D1' => 'Observaciones',
            'E1' => 'Fecha Inicio',
            'F1' => 'Fecha Fin',
            'G1' => 'Año',
        ];

        foreach ($headers as $cell => $label) {
            $this->addHeader($sheet, $cell, $label);
        }

        // Datos reales
        $data = (new OfficeM())->getReporteOficios();

        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue("A{$row}", $item->num_turno_sistema);
            $sheet->setCellValue("B{$row}", $item->documento);
            $sheet->setCellValue("C{$row}", $item->asunto);
            $sheet->setCellValue("D{$row}", $item->observaciones);
            $sheet->setCellValue("E{$row}", $item->fecha_inicio);
            $sheet->setCellValue("F{$row}", $item->fecha_fin);
            $sheet->setCellValue("G{$row}", $item->anio);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="informe_oficios.xlsx"',
            'Cache-Control' => 'max-age=0',
            'Pragma' => 'public',
        ]);
    }

    private function addHeader($sheet, $cell, $value)
    {
        $sheet->setCellValue($cell, $value);
        $style = $sheet->getStyle($cell);
        $style->getFont()->setBold(true);
        $style->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
        $style->getFill()->setFillType(Fill::FILL_SOLID);
        $style->getFill()->getStartColor()->setARGB('FF10312B');
    }
}
