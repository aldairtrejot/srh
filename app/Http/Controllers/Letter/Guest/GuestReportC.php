<?php

namespace App\Http\Controllers\Letter\Guest;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Letter\Collection\CollectionAreaM;
use App\Models\Letter\Collection\CollectionDateM;
use App\Models\Letter\Collection\CollectionStatusM;
use App\Models\Letter\Dashboard\ReportM;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class GuestReportC extends Controller
{
    // Genera reporte de Exel
    public function generate(Request $request)
    {
        try {
            \Log::info('inicio');
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $reportM = new ReportM();

            $query = $reportM->generateReportGuest();

            // Encabezados
            $encabezados = [
                'A' => 'No.',
                'B' => 'Folio de Gestión',
                'C' => 'Oficio Recibido',
                'D' => 'Fecha de Alta',
                'E' => 'Fecha de Vencimiento',
                'F' => 'Puesto del Remitente',
                'G' => 'Asunto',
                'H' => 'Clave',
                'I' => 'Área',
                'J' => 'Copia a',
                'K' => 'Tipo de Documento',
            ];

            foreach ($encabezados as $col => $titulo) {
                $cell = $col . '1';
                $sheet->setCellValue($cell, $titulo);

                // Estilo del encabezado
                $sheet->getStyle($cell)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => Color::COLOR_WHITE],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => '10312B'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Ajuste automático de columna
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Datos
            $row = 2;
            $id = 1;
            foreach ($query as $data) {
                $sheet->setCellValueExplicit('A' . $row, $id, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('B' . $row, $data->folio_gestion, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('C' . $row, $data->num_documento, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('D' . $row, $data->fecha_inicio, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('E' . $row, $data->fecha_fin, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('F' . $row, $data->puesto_remitente, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('G' . $row, $data->asunto, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('H' . $row, $data->clave, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('I' . $row, $data->area, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('J' . $row, $data->area_cc, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('K' . $row, $data->tipo_documento, DataType::TYPE_STRING);

                $row++;
                $id++;
            }

            // Aplicar autofiltros
            $sheet->setAutoFilter("A1:K1");

            // Guardar en stream
            $writer = new Xlsx($spreadsheet);

            return new StreamedResponse(function () use ($writer) {
                if (ob_get_contents())
                    ob_end_clean();
                $writer->save('php://output');
            }, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="DATA_GC_SIRH.xlsx"',
                'Cache-Control' => 'max-age=0',
                'Pragma' => 'public',
            ]);
        } catch (\Throwable $th) {
            \Log::info($th);
        }
    }

    // La función agrega encabezados para las columnas
    private function addStyleValue($sheet, $cell, $value, $background)
    {
        // Aplicar formato 
        $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle($cell)->getFill()->getStartColor()->setARGB($background);
        $sheet->getStyle($cell)->getFont()->setBold(true);
        $sheet->getStyle($cell)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

        // Establecer los valores de las celdas
        $sheet->setCellValue($cell, $value);
    }


    // La funcion agrega estilos asi como valor a una celda 
    private function addStyleTittle($sheet, $cell, $value, $background, $bold, $alignment)
    {
        // Valu
        $sheet->setCellValue($cell, $value);

        // Aplicar bold
        $sheet->getStyle($cell)->getFont()->setBold($bold);

        // Establecer color de fondo
        $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle($cell)->getFill()->getStartColor()->setARGB($background);

        // Establecer alineación según el parámetro de alineación
        $sheet->getStyle($cell)->getAlignment()->setHorizontal($alignment);
    }
}
