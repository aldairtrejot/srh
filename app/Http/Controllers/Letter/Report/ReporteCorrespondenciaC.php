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

        $areasPosiciones = [
            'CAPACITACIÓN' => [45, 91],
            'COORDINACIÓN DE RECURSOS HUMANOS' => [45, 94.6],
            'COORDINACIÓN TÉCNICA DE ESTRUCTURA, ORGANIZACIÓN Y PRESUPUESTO DE SERVICIOS PERSONALES' => [45, 97.8],
            'COORDINACIÓN TÉCNICA DE NÓMINA IMSS-BIENESTAR Y HRAES' => [45, 104],
            'DIVISIÓN DE GESTIÓN DE PERSONAL' => [45, 107.7],
            'DIVISIÓN DE RELACIONES LABORALES' => [45, 110.7],
            'DIVISIÓN DE SISTEMAS DE INFORMACIÓN DE PERSONAL' => [45, 113.7],
            'HRAES' => [45, 116.9],
            'NO CONCURRENTES' => [45, 120.7],
            'OFICINA CENTRAL' => [45, 123.7],
            'RECLUTAMIENTO' => [45, 126.7],
            'UNIDAD DE TRANSPARENCIA' => [45, 129.9],
            'ZONA CENTRO' => [45, 133.5],
            'ZONA NORESTE' => [45, 136.5],
            'ZONA NOROESTE' => [45, 140.1],
            'ZONA SURESTE' => [45, 143.1],
            'ZONA SUROESTE' => [45, 146.1],
        ];

        $pdf->SetFont('ZapfDingbats', '', 9); // Fuente para caracteres especiales como la palomita

        foreach ($areasPosiciones as $area => [$x, $y]) {
            if (in_array($area, $copy)) {
                $pdf->SetTextColor(0, 128, 0); // Verde
                $pdf->SetXY($x, $y);
                $pdf->Write(0, '4'); // Palomita en ZapfDingbats (código 4)
            }
        }


        $pdf->SetFont('Helvetica', '', 9); // Fuente Arial normal
        $pdf->SetTextColor(0, 0, 0);   // Color negro

        //DATA DATE ACTUAL
        $pdf->SetXY(174.5, 38.2); // Posición X, Y en el PDF
        $pdf->Write(0, $fechaActual = now()->format('d/m/Y'));

        //DATA NUM TURNO
        $pdf->SetXY(46, 51); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_turno_sistema);

        //DATA NUM DOCUMENTO
        $pdf->SetXY(46, 57); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_documento);


        //DATA FOLIO DE GESTION
        $pdf->SetXY(46, 63.5); // Posición X, Y en el PDF
        $pdf->Write(0, $data->folio_gestion);

        //FECHA DE INICIO
        $pdf->SetXY(176, 51); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_inicio);

        //FECHA DE FIN 
        $pdf->SetXY(176, 57); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_fin);

        //FECHA DE DOCUMENTO
        $pdf->SetXY(176, 63.5); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_documento);


        //DATA UNIDAD
        $pdf->SetXY(46, 67.8); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->unidad));

        //DATA COORDINACION
        $pdf->SetXY(46, 79.5); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->coordinacion));

        //DATA AREA
        $pdf->SetFont('Helvetica', '', 8); // Fuente Arial normal
        $pdf->SetXY(46, 86); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->area));

        //DATA TRAMITE
        $pdf->SetFont('Helvetica', '', 9); // Fuente Arial normal
        $pdf->SetXY(46, 151.5); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->tramite));

        //DATA CODIGO
        $pdf->SetXY(46, 158.4); // Posición X, Y en el PDF
        $pdf->Write(0, $data->codigo);

        //DATA REMITENTE
        $pdf->SetXY(46, 163.2); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->remitente));

        //DATA PUESTO REMITENTE
        $pdf->SetXY(46, 168.8); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->puesto_remitente));


        //DATA ASUNTO
        $pdf->SetXY(46, 178); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->asunto));

        //DATA LUGAR
        $pdf->SetXY(46, 201); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->entidad));

        //DATA OBSERVACIONES
        $pdf->SetXY(46, 208); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->observaciones));

        //DATA USUARIO
        $pdf->SetXY(46, 228); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->user_area));


        // Enviar el PDF generado al navegador
        return response($pdf->Output('I'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="pdf-modificado.pdf"');
    }
}
