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

        $pdf->AddFont('DejaVuSans', '', 'DejaVuSans.php');
        $pdf->SetFont('DejaVuSans', '', 8); // Usar DejaVuSans para soportar caracteres especiales
        

        //DATA DATE ACTUAL
        $pdf->SetXY(181, 42.5); // Posición X, Y en el PDF
        $pdf->Write(0, $fechaFormateada = now()->format('d/m/Y'));

        // Configurar la fuente para el texto
        $pdf->SetFont('DejaVuSans', '', 9);

        //DATA NO COPIAS
        $pdf->SetXY(170, 167.2); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_copias);

        //DATA NO TOMOS
        $pdf->SetXY(103, 167.2); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_tomos);

        //DATA O FOJAS
        $pdf->SetXY(35.3, 167.2); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_flojas);

        //DATA LUGAR
        $pdf->SetXY(37.2, 147.7); // Posición X, Y en el PDF
        $pdf->Write(0, $data->observaciones);

        //DATA LUGAR
        $pdf->SetXY(37.2, 142.7); // Posición X, Y en el PDF
        $pdf->Write(0, $data->lugar);

        //DATA ASUNTO
        $pdf->SetXY(37.2, 138); // Posición X, Y en el PDF
        $pdf->Write(0, $data->asunto);

        //DATA REMITENTE
        $pdf->SetXY(37.2, 132.7); // Posición X, Y en el PDF
        $pdf->Write(0, $data->remitente);

        //DATA DESCRIPCION
        $pdf->SetXY(32.7, 118.8); // Posición X, Y en el PDF
        $pdf->Write(0, $data->clave);

        //DATA CODIGO
        $pdf->SetXY(32.7, 113.9); // Posición X, Y en el PDF
        $pdf->Write(0, $data->codigo);

        //DATA TRAMITE
        $pdf->SetXY(32.7, 109.2); // Posición X, Y en el PDF
        $pdf->Write(0, $data->tramite);

        //DATA AREA
        $pdf->SetXY(40.5, 95.5); // Posición X, Y en el PDF
        $pdf->Write(0, $data->area);

        //DATA COORDINACION
        $pdf->SetXY(40.5, 90.3); // Posición X, Y en el PDF
        $pdf->Write(0, $data->coordinacion);

        //DATA UNIDAD
        $pdf->SetXY(40.5, 79); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, $data->unidad);

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
