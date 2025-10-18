<?php

namespace App\Http\Controllers\Letter\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Letter\Collection\CollectionAreaM;
use App\Models\Letter\Collection\CollectionDateM;
use App\Models\Letter\Collection\CollectionStatusM;
use App\Models\Letter\Dashboard\ReportM;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardLetterC extends Controller
{
    // La función trae los catalogos iniciales para poblar los catlagos de informe
    public function getCollection()
    {
        // Class
        $collectionAreaM = new CollectionAreaM;
        $collectionStatusM = new CollectionStatusM;
        $collectionDateM = new CollectionDateM;

        // Se obtiene los catalogos a usar en el reporte
        $cat_area_j_1 = $collectionAreaM->getAreaBy1();

        // Se pasa como arreglo vacio porque depende de un catalogo
        $cat_area_j_2 = [];

        $resultCollectionStatus = $collectionStatusM->list();
        $resultCollectionDate = $collectionDateM->list();

        // Send Data
        return response()->json([
            'cat_area_j_1' => $cat_area_j_1,
            'cat_area_j_2' => $cat_area_j_2,
            'resultCollectionStatus' => $resultCollectionStatus,
            'resultCollectionDate' => $resultCollectionDate,
        ]);
    }

    // La función pobla el 2do catalogo de jerarquia 2 dependiendo de la área que seleccione el usuario
    public function setAreaJ2(Request $request)
    {
        // Class
        $collectionAreaM = new CollectionAreaM;

        $cat_area_j_2 = $collectionAreaM->getAreaBy2($request->id_cat_area_j_1);

        // Send Data
        return response()->json([
            'cat_area_j_2' => $cat_area_j_2,
        ]);
    }

    // La función pobla el 2do catalogo de jerarquia 3 dependiendo de la área que seleccione el usuario
    public function setAreaJ3(Request $request)
    {
        // Class
        $collectionAreaM = new CollectionAreaM;

        $cat_area_j_3 = $collectionAreaM->getAreaBy3($request->cat_area_j_2);

        // Send Data
        return response()->json([
            'cat_area_j_3' => $cat_area_j_3,
        ]);
    }

    // Genera reporte de Exel
    public function generate(Request $request)
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $reportM = new ReportM;

        $query = $reportM->generateReport($request);

        // Encabezados
        $encabezados = [
            'A' => 'No.',
            'B' => 'Fólio de Gestión',
            'C' => 'Estatus',
            'D' => 'Oficio Recibido',
            'E' => 'Fecha de Alta',
            'F' => 'Fecha de Vencimiento',
            'G' => 'Puesto del Remitente',
            'H' => 'Asunto',
            'I' => 'C.R.H.',
            'J' => 'C.R.H.T.',
            'K' => 'Área',
            'L' => 'Trámite',
            'M' => 'Clave',
            'N' => 'Tipo de Documento',
            'O' => 'Observaciones',
            'P' => '¿El Fólio Tiene Respuesta?',
            'Q' => 'Descripción de Respuesta',
            'R' => 'Copia para Conocimiento',
            'S' => 'Usuario de Captura',
            'T' => 'Fecha de Captura',
            'U' => 'Hora de Captura',
        ];

        foreach ($encabezados as $col => $titulo) {
            $cell = $col.'1';
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
            $sheet->setCellValueExplicit('A'.$row, $id, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('B'.$row, $data->folio_gestion, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C'.$row, $data->estatus, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('D'.$row, $data->oficio_recibido, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('E'.$row, $data->fecha_alta, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('F'.$row, $data->fecha_fin, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('G'.$row, $data->puesto_remitente, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('H'.$row, $data->asunto, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('I'.$row, $data->c_r_h, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('J'.$row, $data->c_r_h_t, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('K'.$row, $data->area_zona, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('L'.$row, $data->tramite_general, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('M'.$row, $data->tramite_especifico, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('N'.$row, $data->tipo_documento, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('O'.$row, $data->observaciones, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('P'.$row, $data->estatus_respuesta, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('Q'.$row, $data->descripcion_cierre, DataType::TYPE_STRING);

            if ($request->check_copia_a) { // copia a
                $sheet->setCellValueExplicit('R'.$row, $data->copia_a, DataType::TYPE_STRING);
            }

            if ($request->inlcuir_usuario_capturo) {
                if (
                    in_array(1, session('SESSION_ROLE_USER', [])) ||
                    in_array(2, session('SESSION_ROLE_USER', []))
                ) {
                    $sheet->setCellValueExplicit('S'.$row, $data->usuario_captura, DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('T'.$row, $data->fecha_captura, DataType::TYPE_STRING);
                    $sheet->setCellValueExplicit('U'.$row, $data->hora_captura, DataType::TYPE_STRING);
                }

            }

            $row++;
            $id++;
        }

        // Aplicar autofiltros
        $sheet->setAutoFilter('A1:U1');

        // Guardar en stream
        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            if (ob_get_contents()) {
                ob_end_clean();
            }
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
