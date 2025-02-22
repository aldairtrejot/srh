<?php

namespace App\Http\Controllers\Letter\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Letter\Collection\CollectionAreaM;
use App\Models\Letter\Collection\CollectionDateM;
use App\Models\Letter\Collection\CollectionStatusM;
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $this->addStyle($sheet, 'A1', 'NOMBRE:', 'BFBFBF', true, 'HORIZONTAL_LEFT');
        $this->addStyle($sheet, 'A2', 'USUARIO:', 'BFBFBF', true, 'HORIZONTAL_LEFT');
        $this->addStyle($sheet, 'A3', 'FECHA DE EMISIÓN:', 'BFBFBF', true, 'HORIZONTAL_LEFT');

        $this->addStyle($sheet, 'B1', 'INFORME DE GESTIÓN DE CONTROL', 'E8E8E8', false, 'HORIZONTAL_LEFT');
        $this->addStyle($sheet, 'B2', Auth::user()->name, 'E8E8E8', false, 'HORIZONTAL_LEFT');
        $this->addStyle($sheet, 'B3', $carbon->format('d/m/Y'), 'E8E8E8', false, 'HORIZONTAL_LEFT');




        // Aplicar formato a la fila 5 (A5 a N5)
        $sheet->getStyle('A5:N5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A5:N5')->getFill()->getStartColor()->setARGB('10312B');
        $sheet->getStyle('A5:N5')->getFont()->setBold(true);
        $sheet->getStyle('A5:N5')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

        // Establecer los valores de las celdas A5, B5, C5
        $sheet->setCellValue('A5', 'Descripcion A');
        $sheet->setCellValue('B5', 'Descripcion B');
        $sheet->setCellValue('C5', 'Descripcion C');



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

    // La funcion agrega estilos asi como valor a una celda 
    function addStyle($sheet, $cell, $value, $background, $bold, $alignment)
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
