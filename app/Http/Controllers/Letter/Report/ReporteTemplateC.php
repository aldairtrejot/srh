<?php

namespace App\Http\Controllers\Letter\Report;

use App\Models\Letter\Collection\CollectionReportM;
use setasign\Fpdi\Fpdi;
use App\Http\Controllers\Controller;

class ReporteTemplateC extends Controller
{

    //Generar el reporte de expedientes
    public function file($id)
    {
        $collectionReportM = new CollectionReportM();
        $reporteTemplateC = new ReporteTemplateC();

        //Value
        $tableName = 'correspondencia.tbl_expediente';
        $idTable = 'id_tbl_expediente';
        $data = $collectionReportM->templateReporte($id, $tableName, $idTable);
        $reporteTemplateC->generatePdf($data);
    }

    //Generar el reporte de circulares
    public function round($id)
    {
        $collectionReportM = new CollectionReportM();
        $reporteTemplateC = new ReporteTemplateC();

        //Value
        $tableName = 'correspondencia.tbl_circular';
        $idTable = 'id_tbl_circular';
        $data = $collectionReportM->templateReporte($id, $tableName, $idTable);
        $reporteTemplateC->generatePdf($data);
    }

    //Generar el reporte de odicio
    public function office($id)
    {
        $collectionReportM = new CollectionReportM();
        $reporteTemplateC = new ReporteTemplateC();

        //Value
        $tableName = 'correspondencia.tbl_oficio';
        $idTable = 'id_tbl_oficio';
        $data = $collectionReportM->templateReporte($id, $tableName, $idTable);
        $reporteTemplateC->generatePdf($data);
    }


    //Reporte de interno
    public function inside($id)
    {
        $collectionReportM = new CollectionReportM();
        $reporteTemplateC = new ReporteTemplateC();

        //Value
        $tableName = 'correspondencia.tbl_interno';
        $idTable = 'id_tbl_interno';
        $data = $collectionReportM->templateReporte($id, $tableName, $idTable);
        $reporteTemplateC->generatePdf($data);
    }

    //La funcion genera el reporte
    private function generatePdf($data)
    {
        $pdfPath = public_path('assets/documents/template-pdf/template_correspondencia_interno.pdf'); // Ruta del archivo PDF existenteF
        $pdf = new Fpdi(); // Instancia de FPDI (requiere TCPDF o FPDF)
        $pdf->setSourceFile($pdfPath); // Cargar la plantilla PDF existente
        $template = $pdf->importPage(1); // Importar la primera página del PDF existente
        $pdf->addPage(); // Agregar una página en blanco
        $pdf->useTemplate($template); // Usar la plantilla importada

        $pdf->SetFont('arial', '', 9); // Usar DejaVuSans para soportar caracteres especiales

        //DATA DATE ACTUAL
        $pdf->SetXY(175, 52.8); // Posición X, Y en el PDF
        $pdf->Write(0, $fechaActual = now()->format('d/m/Y'));

        // Configurar la fuente para el texto
        $pdf->SetFont('arial', '', 9);

        //DATA NUM TURNO
        $pdf->SetXY(40.5, 65); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_turno_sistema);

        //AÑO 
        $pdf->SetXY(147, 65); // Posición X, Y en el PDF
        $pdf->Write(0, $data->anio);

        //FECHA DE INICIO
        $pdf->SetXY(40.5, 59); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_inicio);

        //FECHA DE FIN 
        $pdf->SetXY(147, 59); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_fin);

        //DATA NUM DOCUMENTO
        $pdf->SetXY(40.5, 71); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_correspondencia);

        //DATA ASUNTO
        $pdf->SetXY(40.5, 78.2); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->asunto));

        //DATA LUGAR
        $pdf->SetXY(40.5, 88); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->observaciones));

        // Enviar el PDF generado al navegador
        return response($pdf->Output('I'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="pdf-modificado.pdf"');
    }
}
