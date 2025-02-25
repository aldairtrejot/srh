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

    public function generate(Request $request)
    {
        // Class
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $carbon = Carbon::now(); //Hora y fecha actual
        $reportM = new ReportM();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $query = $reportM->generateReport(
            $request->id_cat_area,
            $request->id_cat_status,
            $request->fecha_inicio_fecha_fin,
            $request->fecha_inicio_informe,
            $request->fecha_fin_informe,
            $request->id_cat_date_informe,
        );

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
        $this->addStyleValue($sheet, 'G6', 'FECHA INICIO', '10312B');
        $this->addStyleValue($sheet, 'H6', 'FECHA FIN', '10312B');
        $this->addStyleValue($sheet, 'I6', 'FECHA DOCUMENTO', '10312B');
        $this->addStyleValue($sheet, 'J6', 'ASUNTO', '10312B');
        $this->addStyleValue($sheet, 'K6', 'OBSERVACIONES', '10312B');
        $this->addStyleValue($sheet, 'L6', 'ÁREA', '10312B');
        $this->addStyleValue($sheet, 'M6', 'USUARIO TITULAR', '10312B');
        $this->addStyleValue($sheet, 'N6', 'USUARIO ENLACE', '10312B');
        $this->addStyleValue($sheet, 'O6', 'UNIDAD', '10312B');
        $this->addStyleValue($sheet, 'P6', 'COORDINACIÓN', '10312B');
        $this->addStyleValue($sheet, 'Q6', 'TRAMITE', '10312B');
        $this->addStyleValue($sheet, 'R6', 'CLAVE', '10312B');
        $this->addStyleValue($sheet, 'S6', 'HRS. RESPUESTA', '10312B');
        $this->addStyleValue($sheet, 'T6', 'DOCUMENTO', '10312B');
        $this->addStyleValue($sheet, 'U6', 'LUGAR', '10312B');
        $this->addStyleValue($sheet, 'V6', 'REMITENTE', '10312B');
        $this->addStyleValue($sheet, 'W6', 'PUESTO REMITENTE', '10312B');


        if ($request->inlcuir_usuario_capturo) {
            $this->addStyleValue($sheet, 'X6', 'FECHA CAPTURA', '10312B');
            $this->addStyleValue($sheet, 'Y6', 'HORA CAPTURA', '10312B');
            $this->addStyleValue($sheet, 'Z6', 'USUARIO CAPTURA', '10312B');
        }



        $row = 7; // Empezamos desde la fila 2
        $id = 1;
        foreach ($query as $data) {
            $sheet->setCellValue('A' . $row, $id);
            $sheet->setCellValue('B' . $row, $data->folio_gestion);
            $sheet->setCellValue('C' . $row, $data->num_documento);
            $sheet->setCellValue('D' . $row, $data->num_turno_sistema);
            $sheet->setCellValue('E' . $row, $data->estatus);
            $sheet->setCellValue('F' . $row, $data->anio);
            $sheet->setCellValue('G' . $row, $data->fecha_inicio);
            $sheet->setCellValue('H' . $row, $data->fecha_fin);
            $sheet->setCellValue('I' . $row, $data->fecha_documento);
            $sheet->setCellValue('J' . $row, $data->asunto);
            $sheet->setCellValue('K' . $row, $data->observaciones);
            $sheet->setCellValue('L' . $row, $data->area);
            $sheet->setCellValue('M' . $row, $data->titular);
            $sheet->setCellValue('N' . $row, $data->enlace);
            $sheet->setCellValue('O' . $row, $data->unidad);
            $sheet->setCellValue('P' . $row, $data->coordinacion);
            $sheet->setCellValue('Q' . $row, $data->tramite);
            $sheet->setCellValue('R' . $row, $data->clave);
            $sheet->setCellValue('S' . $row, $data->horas_respuesta);
            $sheet->setCellValue('T' . $row, $data->tipo_documento);
            $sheet->setCellValue('U' . $row, $data->entidad);
            $sheet->setCellValue('V' . $row, $data->remitente);
            $sheet->setCellValue('W' . $row, $data->puesto_remitente);

            if ($request->inlcuir_usuario_capturo) {
                $sheet->setCellValue('X' . $row, $data->fecha_captura);
                $sheet->setCellValue('Y' . $row, $data->hora_captura);
                $sheet->setCellValue('Z' . $row, $data->usuario_add);
            }
            $row++;
            $id++;
        }

        $this->addStyleTittle($sheet, 'B4', ($id - 1), 'E8E8E8', false, 'HORIZONTAL_LEFT');

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
