<?php

namespace App\Http\Controllers\Letter\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardOfficeC extends Controller
{
    public function generate()
    {
        ob_end_clean(); // 🔥 Mata cualquier buffer previo

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $this->addHeader($sheet, 'A1', 'ID');
        $this->addHeader($sheet, 'B1', 'Nombre');
        $this->addHeader($sheet, 'C1', 'Fecha');
        $sheet->setAutoFilter('A1:C1');

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="reporte_oficios_encabezados.xlsx"',
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
        $style->getFill()->getStartColor()->setARGB('FF10312B'); // Verde con alpha FF
    }
}
