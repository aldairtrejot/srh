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
        $resultCollectionArea = $collectionAreaM->list();
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
        // Class
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $carbon = Carbon::now(); //Hora y fecha actual
        $reportM = new ReportM();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $query = $reportM->generateReport( // parametros de funcion
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

        // Encabezado inicial
        //$this->addStyleTittle($sheet, 'A1', 'NOMBRE:', 'BFBFBF', true, 'HORIZONTAL_LEFT');
        //$this->addStyleTittle($sheet, 'A2', 'USUARIO:', 'BFBFBF', true, 'HORIZONTAL_LEFT');
        //$this->addStyleTittle($sheet, 'A3', 'FECHA DE EMISIÓN:', 'BFBFBF', true, 'HORIZONTAL_LEFT');
        //$this->addStyleTittle($sheet, 'A4', 'TOTAL:', 'BFBFBF', true, 'HORIZONTAL_LEFT');

        //$this->addStyleTittle($sheet, 'B1', 'INFORME DE GESTIÓN DE CONTROL', 'E8E8E8', false, 'HORIZONTAL_LEFT');
        //$this->addStyleTittle($sheet, 'B2', Auth::user()->name, 'E8E8E8', false, 'HORIZONTAL_LEFT');
        //$this->addStyleTittle($sheet, 'B3', $carbon->format('d/m/Y'), 'E8E8E8', false, 'HORIZONTAL_LEFT');

        // Valor de encabezados
        $this->addStyleValue($sheet, 'A1', 'Folio de Gestión', '10312B');
        $this->addStyleValue($sheet, 'B1', 'Oficio Recibido', '10312B');
        $this->addStyleValue($sheet, 'C1', 'Fecha de Alta', '10312B');
        $this->addStyleValue($sheet, 'D1', 'Fecha Vencimiento', '10312B');
        $this->addStyleValue($sheet, 'E1', 'Puesto del Remitente', '10312B');
        $this->addStyleValue($sheet, 'F1', 'Asunto', '10312B');
        $this->addStyleValue($sheet, 'G1', 'Trámite', '10312B');
        $this->addStyleValue($sheet, 'H1', 'Unidad', '10312B');
        $this->addStyleValue($sheet, 'I1', 'Coordinación', '10312B');


        if ($request->inlcuir_usuario_capturo) { //  validacion para incluir datos de captura
            $this->addStyleValue($sheet, 'J1', 'Fecha de Captura', '10312B');
            $this->addStyleValue($sheet, 'K1', 'Hora de Captura', '10312B');
            $this->addStyleValue($sheet, 'L1', 'Usuario que Captura', '10312B');
        }



        $row = 2; // Empezamos desde la fila 2
        $id = 1; // id que incrementa
        foreach ($query as $data) { // insert de datos
            // Cambia las líneas en las que estás estableciendo los valores de las celdas como texto:
            $sheet->setCellValueExplicit('A' . $row, $data->folio_gestion, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('B' . $row, $data->num_documento, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $row, $data->fecha_inicio, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('D' . $row, $data->fecha_fin, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('E' . $row, $data->puesto_remitente, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('F' . $row, $data->asunto, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('G' . $row, $data->tramite, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('H' . $row, $data->unidad, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('I' . $row, $data->coordinacion, DataType::TYPE_STRING);

            if ($request->inlcuir_usuario_capturo) {
                $sheet->setCellValueExplicit('J' . $row, $data->fecha_captura, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('K' . $row, $data->hora_captura, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('L' . $row, $data->usuario_add, DataType::TYPE_STRING);
            }


            $row++;
            $id++;
        }

        // Total de registros
        //$this->addStyleTittle($sheet, 'B4', ($id - 1), 'E8E8E8', false, 'HORIZONTAL_LEFT');

        // Se incluyen filtros en encabezados
        if ($request->inlcuir_usuario_capturo) {
            $sheet->setAutoFilter('A1:L1');
        } else {
            $sheet->setAutoFilter('A1:I1');
        }


        // Escribir en memoria
        $writer = new Xlsx($spreadsheet);
        $fileName = 'DATA_GC_SIRH.xlsx';

        // Crear una respuesta en formato binario
        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
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
