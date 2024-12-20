<?php

namespace App\Http\Controllers\Letter\Report;
use App\Models\Letter\Letter\LetterM;
use setasign\Fpdi\Fpdi;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReporteCorrespondenciaC extends Controller
{
    public function generatePdf($id)
    {
        $LetterM = new LetterM();
        $data = $LetterM->getDataReport($id);

        $pdfPath = public_path('assets/documents/template-pdf/templateCorrespondencia.pdf'); // Ruta del archivo PDF existenteF
        $pdf = new Fpdi(); // Instancia de FPDI (requiere TCPDF o FPDF)
        $pdf->setSourceFile($pdfPath); // Cargar la plantilla PDF existente
        $template = $pdf->importPage(1); // Importar la primera página del PDF existente
        $pdf->addPage(); // Agregar una página en blanco
        $pdf->useTemplate($template); // Usar la plantilla importada
        $fechaActual = Carbon::now(); //Fecha actual para el reporte

        $pdf->SetFont('arial', '', 8); // Usar DejaVuSans para soportar caracteres especiales


        //DATA DATE ACTUAL
        $pdf->SetXY(181, 42.5); // Posición X, Y en el PDF
        $pdf->Write(0, $fechaActual = now()->format('d/m/Y'));

        // Configurar la fuente para el texto
        $pdf->SetFont('arial', '', 9);

        //DATA NO COPIAS
        $pdf->SetXY(170, 182); // Posición X, Y en el PDF
        $pdf->Write(0, $data->horas_respuesta);

        //DATA NO TOMOS
        $pdf->SetXY(103, 182); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_tomos);

        //DATA O FOJAS
        $pdf->SetXY(40.5, 182); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_flojas);

        //DATA LUGAR
        $pdf->SetXY(40.5, 166); // Posición X, Y en el PDF
        $pdf->MultiCell(0,4, utf8_decode($data->observaciones));

        //DATA LUGAR
        $pdf->SetXY(40.5, 156.5); // Posición X, Y en el PDF
        $pdf->MultiCell(0,4, utf8_decode($data->lugar));

        //DATA ASUNTO
        $pdf->SetXY(40.5, 146.5); // Posición X, Y en el PDF
        $pdf->MultiCell(0,4, utf8_decode($data->asunto));

        //DATA ASUNTO
        $pdf->SetXY(40.5, 141.2); // Posición X, Y en el PDF
        $pdf->MultiCell(0,4, utf8_decode($data->puesto_remitente));

        //DATA REMITENTE
        $pdf->SetXY(40.5, 131); // Posición X, Y en el PDF
        $pdf->MultiCell(0,4, utf8_decode($data->remitente));

        //DATA DESCRIPCION
        $pdf->SetXY(40.5, 113); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->clave));

        //DATA CODIGO
        $pdf->SetXY(40.5, 109.5); // Posición X, Y en el PDF
        $pdf->Write(0, $data->codigo);

        //DATA TRAMITE
        $pdf->SetXY(40.5, 104.5); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->tramite));

        //DATA AREA
        $pdf->SetXY(40.5, 95.5); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->area));

        //DATA COORDINACION
        $pdf->SetXY(40.5, 90.3); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->coordinacion));

        //DATA UNIDAD
        $pdf->SetXY(40.5, 79); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->unidad));

        //AÑO 
        $pdf->SetXY(147, 65); // Posición X, Y en el PDF
        $pdf->Write(0, $data->anio);

        //FECHA DE INICIO
        $pdf->SetXY(40.5, 59); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_inicio);

        //FECHA DE FIN 
        $pdf->SetXY(147, 59); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_fin);

        //DATA NUM TURNO
        $pdf->SetXY(40.5, 65); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_turno_sistema);

        //DATA NUM DOCUMENTO
        $pdf->SetXY(40.5, 71); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_documento);

        // Enviar el PDF generado al navegador
        return response($pdf->Output('I'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="pdf-modificado.pdf"');
    }
}
