<?php

namespace App\Http\Controllers\Courses\Tableinstructor;

use App\Models\Courses\Courses\Instructores\Instructores\InstructorM;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;

class ReporteConstanciaC extends Controller
{
    
    public function generatePdf($id)
    {
        $InstructorM = new InstructorM ();

        $data = $InstructorM ->getDataReport($id);

        $pdfPath = public_path('assets/documents/template-pdf/templateConstancia.pdf'); // Ruta del archivo PDF existenteF
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
        $pdf->SetXY(163, 54.9); // Posición X, Y en el PDF
        $pdf->Write(0, $data->anio);

        //DATA NUM TURNO
        $pdf->SetXY(57, 72.2); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_turno_sistema);

        //DATA NUM DOCUMENTO
        $pdf->SetXY(57, 78.3); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_documento);


        //DATA FOLIO DE GESTION
        $pdf->SetXY(57, 84.4); // Posición X, Y en el PDF
        $pdf->Write(0, $data->folio_gestion);

        //FECHA DE INICIO
        $pdf->SetXY(177, 71.8); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_inicio);

        //FECHA DE FIN 
        $pdf->SetXY(177, 78); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_fin);

        //FECHA DE DOCUMENTO
        $pdf->SetXY(177, 84.8); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_documento);

        //DATA UNIDAD
        $pdf->SetXY(57, 94.5); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->unidad));

        //DATA COORDINACION
        $pdf->SetXY(57, 106.8); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->coordinacion));

        //DATA AREA
        $pdf->SetXY(57, 113.5); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->area));

        //DATA TRAMITE
        $pdf->SetXY(57, 120.4); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->tramite));

        //DATA CODIGO
        $pdf->SetXY(57, 126.9); // Posición X, Y en el PDF
        $pdf->Write(0, $data->codigo);

        //DATA REMITENTE
        $pdf->SetXY(57, 132); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->remitente));

        //DATA PUESTO REMITENTE
        $pdf->SetXY(57, 137.8); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->puesto_remitente));


        //DATA ASUNTO
        $pdf->SetXY(57, 147.5); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->asunto));

        //DATA LUGAR
        $pdf->SetXY(57, 172); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->entidad));

        //DATA LUGAR
        $pdf->SetXY(57, 178.8); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->observaciones));

        //DATA CODIGO
        $pdf->SetXY(57, 200.7); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->user_area));
        /*
        //DATA NO COPIAS
        $pdf->SetXY(170, 167.5); // Posición X, Y en el PDF
        $pdf->Write(0, $data->horas_respuesta);

        //DATA NO TOMOS
        $pdf->SetXY(103, 167.5); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_tomos);

        //DATA O FOJAS
        $pdf->SetXY(40.5, 167.5); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_flojas);




        //DATA DESCRIPCION
        $pdf->SetXY(40.5, 113); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->clave));




*/
        // Enviar el PDF generado al navegador
        return response($pdf->Output('I'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="pdf-modificado.pdf"');
    }

    }










