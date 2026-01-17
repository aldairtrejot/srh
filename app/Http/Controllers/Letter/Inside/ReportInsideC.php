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
        $pdf->SetXY(49, 73.5); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_turno_sistema);

        //DATA num_documento_area
        $pdf->SetXY(49, 79.8); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_documento_area);

        //DATA anio
        $pdf->SetXY(49, 86.5); // Posición X, Y en el PDF
        $pdf->Write(0, $data->anio);

        // DATA area
        $pdf->SetXY(49, 96); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->area));

        // DATA asunto
        $pdf->SetXY(49, 107); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->asunto));

        // DATA destinatario
        $pdf->SetXY(49, 130); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->destinatario));

        // DATA observaciones
        $pdf->SetXY(49, 150); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->observaciones));


        // Enviar el PDF generado al navegador
        return response($pdf->Output('I'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="pdf-modificado.pdf"');
    }
}
