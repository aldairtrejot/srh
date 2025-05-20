<?php

namespace App\Http\Controllers\Letter\Report;
use App\Models\Letter\Collection\CollectionAreaM;
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
        $collectionAreaM = new CollectionAreaM();
        $data = $LetterM->getDataReport($id);
        $copy = $collectionAreaM->getDataCopia($id);

        $pdfPath = public_path('assets/documents/template-pdf/templateCorrespondencia.pdf'); // Ruta del archivo PDF existenteF
        $pdf = new Fpdi(); // Instancia de FPDI (requiere TCPDF o FPDF)
        $pdf->setSourceFile($pdfPath); // Cargar la plantilla PDF existente
        $template = $pdf->importPage(1); // Importar la primera página del PDF existente
        $pdf->addPage(); // Agregar una página en blanco
        $pdf->useTemplate($template); // Usar la plantilla importada
        $fechaActual = Carbon::now(); //Fecha actual para el reporte

        $pdf->SetFont('arial', '', 9); // Usar DejaVuSans para soportar caracteres especiales


        //DATA DATE ACTUAL
        $pdf->SetXY(163, 48); // Posición X, Y en el PDF
        $pdf->Write(0, $fechaActual = now()->format('d/m/Y'));

        // Configurar la fuente para el texto
        $pdf->SetFont('arial', '', 9);

        //AÑO 
        $pdf->SetXY(163, 54.2); // Posición X, Y en el PDF
        $pdf->Write(0, $data->anio);

        //DATA NUM TURNO
        $pdf->SetXY(57, 69.2); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_turno_sistema);

        //DATA NUM DOCUMENTO
        $pdf->SetXY(57, 75.3); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_documento);


        //DATA FOLIO DE GESTION
        $pdf->SetXY(57, 81.4); // Posición X, Y en el PDF
        $pdf->Write(0, $data->folio_gestion);

        //FECHA DE INICIO
        $pdf->SetXY(177, 69.8); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_inicio);

        //FECHA DE FIN 
        $pdf->SetXY(177, 75); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_fin);

        //FECHA DE DOCUMENTO
        $pdf->SetXY(177, 81.8); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_documento);

        //DATA UNIDAD
        $pdf->SetXY(57, 91.5); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->unidad));

        //DATA COORDINACION
        $pdf->SetXY(57, 103.8); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->coordinacion));

        //DATA AREA
        $pdf->SetXY(57, 110.5); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->area));

        //DATA TRAMITE
        $pdf->SetXY(57, 117.4); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->tramite));

        //DATA CODIGO
        $pdf->SetXY(57, 123.9); // Posición X, Y en el PDF
        $pdf->Write(0, $data->codigo);

        //DATA REMITENTE
        $pdf->SetXY(57, 129); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->remitente));

        //DATA PUESTO REMITENTE
        $pdf->SetXY(57, 134.8); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->puesto_remitente));


        //DATA ASUNTO
        $pdf->SetXY(57, 144.5); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->asunto));

        //DATA LUGAR
        $pdf->SetXY(57, 167); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->entidad));

        //DATA LUGAR
        $pdf->SetXY(57, 175); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->observaciones));

        //DATA USUARIO
        $pdf->SetXY(57, 194.8); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->user_area));

        // DATA COPIA
        $pdf->SetFont('arial', '', 6); // Usar DejaVuSans para soportar caracteres especiales
        $pdf->SetXY(57, 199.0); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($copy));

        // Enviar el PDF generado al navegador
        return response($pdf->Output('I'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="pdf-modificado.pdf"');
    }
}
