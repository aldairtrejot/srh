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
        $this->addStyleTittle($sheet, 'A1', 'NOMBRE:', 'BFBFBF', true, 'HORIZONTAL_LEFT');
        $this->addStyleTittle($sheet, 'A2', 'USUARIO:', 'BFBFBF', true, 'HORIZONTAL_LEFT');
        $this->addStyleTittle($sheet, 'A3', 'FECHA DE EMISIÓN:', 'BFBFBF', true, 'HORIZONTAL_LEFT');
        $this->addStyleTittle($sheet, 'A4', 'TOTAL:', 'BFBFBF', true, 'HORIZONTAL_LEFT');

        $this->addStyleTittle($sheet, 'B1', 'INFORME DE GESTIÓN DE CONTROL', 'E8E8E8', false, 'HORIZONTAL_LEFT');
        $this->addStyleTittle($sheet, 'B2', Auth::user()->name, 'E8E8E8', false, 'HORIZONTAL_LEFT');
        $this->addStyleTittle($sheet, 'B3', $carbon->format('d/m/Y'), 'E8E8E8', false, 'HORIZONTAL_LEFT');

        // Valor de encabezados
        $this->addStyleValue($sheet, 'A6', 'ID', '10312B');
        $this->addStyleValue($sheet, 'B6', 'FOL. GESTIÓN', '10312B');
        $this->addStyleValue($sheet, 'C6', 'NO. DOCUMENTO', '10312B');
        $this->addStyleValue($sheet, 'D6', 'NO. SISTEMA', '10312B');
        $this->addStyleValue($sheet, 'E6', 'ESTATUS', '10312B');
        $this->addStyleValue($sheet, 'F6', 'AÑO', '10312B');
        $this->addStyleValue($sheet, 'G6', 'FECHA CAPTURA', '10312B');
        $this->addStyleValue($sheet, 'H6', 'FECHA INICIO', '10312B');
        $this->addStyleValue($sheet, 'I6', 'FECHA FIN', '10312B');
        $this->addStyleValue($sheet, 'J6', 'FECHA DOCUMENTO', '10312B');
        $this->addStyleValue($sheet, 'K6', 'ASUNTO', '10312B');
        $this->addStyleValue($sheet, 'L6', 'OBSERVACIONES', '10312B');
        $this->addStyleValue($sheet, 'M6', 'ÁREA', '10312B');
        $this->addStyleValue($sheet, 'N6', 'USUARIO TITULAR', '10312B');
        $this->addStyleValue($sheet, 'O6', 'USUARIO ENLACE', '10312B');
        $this->addStyleValue($sheet, 'P6', 'UNIDAD', '10312B');
        $this->addStyleValue($sheet, 'Q6', 'COORDINACIÓN', '10312B');
        $this->addStyleValue($sheet, 'R6', 'TRAMITE', '10312B');
        $this->addStyleValue($sheet, 'S6', 'CLAVE', '10312B');
        $this->addStyleValue($sheet, 'T6', 'HRS. RESPUESTA', '10312B');
        $this->addStyleValue($sheet, 'U6', 'DOCUMENTO', '10312B');
        $this->addStyleValue($sheet, 'V6', 'LUGAR', '10312B');
        $this->addStyleValue($sheet, 'W6', 'REMITENTE', '10312B');
        $this->addStyleValue($sheet, 'X6', 'PUESTO REMITENTE', '10312B');


        if ($request->inlcuir_usuario_capturo) { //  validacion para incluir datos de captura
            $this->addStyleValue($sheet, 'Y6', 'FECHA CAPTURA', '10312B');
            $this->addStyleValue($sheet, 'Z6', 'HORA CAPTURA', '10312B');
            $this->addStyleValue($sheet, 'AA6', 'USUARIO CAPTURA', '10312B');
        }



        $row = 7; // Empezamos desde la fila 2
        $id = 1; // id que incrementa
        foreach ($query as $data) { // insert de datos
            // Cambia las líneas en las que estás estableciendo los valores de las celdas como texto:
            $sheet->setCellValueExplicit('A' . $row, $id, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('B' . $row, $data->folio_gestion, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $row, $data->num_documento, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('D' . $row, $data->num_turno_sistema, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('E' . $row, $data->estatus, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('F' . $row, $data->anio, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('G' . $row, $data->fecha_captura, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('H' . $row, $data->fecha_inicio, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('I' . $row, $data->fecha_fin, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('J' . $row, $data->fecha_documento, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('K' . $row, $data->asunto, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('L' . $row, $data->observaciones, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('M' . $row, $data->area, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('N' . $row, $data->titular, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('O' . $row, $data->enlace, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('P' . $row, $data->unidad, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('Q' . $row, $data->coordinacion, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('R' . $row, $data->tramite, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('S' . $row, $data->clave, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('T' . $row, $data->horas_respuesta, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('U' . $row, $data->tipo_documento, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('V' . $row, $data->entidad, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('W' . $row, $data->remitente, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('X' . $row, $data->puesto_remitente, DataType::TYPE_STRING);

            if ($request->inlcuir_usuario_capturo) {
                $sheet->setCellValueExplicit('Y' . $row, $data->fecha_captura, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('Z' . $row, $data->hora_captura, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('AA' . $row, $data->usuario_add, DataType::TYPE_STRING);
            }


            $row++;
            $id++;
        }

        // Total de registros
        $this->addStyleTittle($sheet, 'B4', ($id - 1), 'E8E8E8', false, 'HORIZONTAL_LEFT');

        // Se incluyen filtros en encabezados
        if ($request->inlcuir_usuario_capturo) {
            $sheet->setAutoFilter('A6:AA6');
        } else {
            $sheet->setAutoFilter('A6:X6');
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
