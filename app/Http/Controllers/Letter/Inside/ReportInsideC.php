<?php

namespace App\Http\Controllers\Letter\Inside;

use App\Models\Letter\Inside\InsideM;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;
class ReportInsideC extends Controller
{
    // La función retorna el documento pdf para internos
    public function report($id)
    {
        $insideM = new InsideM();
        $pdfPath = public_path('assets/documents/template-pdf/template_correspondencia_interno.pdf'); // Ruta del archivo PDF existenteF
        $pdf = new Fpdi(); // Instancia de FPDI (requiere TCPDF o FPDF)
        $pdf->setSourceFile($pdfPath); // Cargar la plantilla PDF existente
        $template = $pdf->importPage(1); // Importar la primera página del PDF existente
        $pdf->addPage(); // Agregar una página en blanco
        $pdf->useTemplate($template); // Usar la plantilla importada
        $data = $insideM->getReport($id);
        $pdf->SetFont('arial', '', 9); // Usar DejaVuSans para soportar caracteres especiales

        //DATA DATE ACTUAL
        $pdf->SetXY(175, 52.8); // Posición X, Y en el PDF
        $pdf->Write(0, $fechaActual = now()->format('d/m/Y'));

        // Configurar la fuente para el texto
        $pdf->SetFont('arial', '', 9);

        //DATA fecha_captura
        $pdf->SetXY(176, 73.5); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_captura);

        //DATA fecha_emision
        $pdf->SetXY(176, 79.8); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_emision);

        //DATA fecha_aplicacion
        $pdf->SetXY(176, 86.5); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_aplicacion);

        //DATA fecha_aplicacion
        $pdf->SetXY(100, 73.5); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_turno_sistema);

        /*
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

        */

        // Enviar el PDF generado al navegador
        return response($pdf->Output('I'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="pdf-modificado.pdf"');
    }
}
