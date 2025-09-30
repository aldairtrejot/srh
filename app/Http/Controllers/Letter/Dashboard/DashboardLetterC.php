<?php

namespace App\Http\Controllers\Letter\Dashboard;
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

class DashboardLetterC extends Controller
{
    // La función trae los catalogos iniciales para poblar los catlagos de informe
    public function getCollection()
    {
        // Class 
        $collectionAreaM = new CollectionAreaM();
        $collectionStatusM = new CollectionStatusM();
        $collectionDateM = new CollectionDateM();

        // Se obtienen los catalogos
        $resultCollectionArea = $collectionAreaM->listLetter();
        $resultCollectionStatus = $collectionStatusM->list();
        $resultCollectionDate = $collectionDateM->list();

        // Send Data
        return response()->json([
            'resultCollectionArea' => $resultCollectionArea,
            'resultCollectionStatus' => $resultCollectionStatus,
            'resultCollectionDate' => $resultCollectionDate,
        ]);
    }

    // Genera reporte de Exel
    public function generate(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $reportM = new ReportM();

        $query = $reportM->generateReport(
            $request->id_cat_area,
            $request->id_cat_status,
            $request->fecha_inicio_fecha_fin,
            $request->fecha_inicio_informe,
            $request->fecha_fin_informe,
            $request->id_cat_date_informe,
            $request->incluir_horas,
            $request->inicio,
            $request->fin,
        );

        // Encabezados
        $encabezados = [
            'A' => 'No.',
            'B' => 'Folio de Gestión',
            'C' => 'Estatus',
            'D' => 'Oficio Recibido',
            'E' => 'Fecha de Alta',
            'F' => 'Fecha de Vencimiento',
            'G' => 'Puesto del Remitente',
            'H' => 'Asunto',
            'I' => 'Clave',
            'J' => 'Área',
            'K' => 'Copia a',
            'L' => 'Tipo de Documento',
            'M' => 'Observaciones',
        ];

        if ($request->inlcuir_usuario_capturo) {
            $encabezados['N'] = 'Fecha de Captura';
            $encabezados['O'] = 'Hora de Captura';
            $encabezados['P'] = 'Usuario que Captura';
        }

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
            $sheet->setCellValueExplicit('C' . $row, $data->estatus, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('D' . $row, $data->num_documento, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('E' . $row, $data->fecha_inicio, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('F' . $row, $data->fecha_fin, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('G' . $row, $data->puesto_remitente, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('H' . $row, $data->asunto, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('I' . $row, $data->clave, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('J' . $row, $data->area, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('K' . $row, $data->area_cc, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('L' . $row, $data->tipo_documento, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('M' . $row, $data->observaciones, DataType::TYPE_STRING);

            if ($request->inlcuir_usuario_capturo) {
                $sheet->setCellValueExplicit('N' . $row, $data->fecha_captura, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('O' . $row, $data->hora_captura, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('P' . $row, $data->usuario_add, DataType::TYPE_STRING);
            }

            $row++;
            $id++;
        }

        // Aplicar autofiltros
        $ultimaCol = $request->inlcuir_usuario_capturo ? 'N' : 'P';
        $sheet->setAutoFilter("A1:{$ultimaCol}1");

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
