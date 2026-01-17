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
            'COORDINACIÓN DE RECURSOS HUMANOS' => [45.2, 96.5],
            'COORDINACIÓN TÉCNICA DE CAPACITACIÓN Y EVALUACIÓN' => [45.2, 99.7],
            'COORDINACIÓN TECNICA DE MOVIMIENTOS DE PERSONAL' => [45.2, 106.5],
            'COORDINACIÓN TÉCNICA DE NÓMINA' => [45.2, 114],
            'DIVISIÓN DE COMUNICACIÓN Y CULTURA LABORAL' => [45.2, 117.5],
            'DIVISIÓN DE GESTIÓN DE PERSONAL' => [45.2, 120.5],
            'DIVISIÓN DE INGRESOS' => [45.2, 124.5],
            'DIVISIÓN DE INTEGRACIÓN Y VALIDACIÓN DE NÓMINA' => [45.2, 127.8],
            'DIVISIÓN DE ORGANIZACIÓN' => [45.2, 131.1],
            'DIVISIÓN DE PRESTACIONES SOCIALES Y ECONÓMICAS' => [45.2, 134.6],
            'DIVISIÓN DE PROCESAMIENTO DE NÓMINA Y PAGO A TERCEROS' => [45.2, 138.1],
            'DIVISIÓN DE RECLUTAMIENTO Y SELECCIÓN' => [45.2, 145],
            'DIVISIÓN DE SISTEMAS DE INFORMACIÓN DE PERSONAL' => [45.2, 149],
            'HRAES' => [140.6, 96.5],
            'NO CONCURRENTES' => [140.6, 99.7],
            'NO DEFINIDA' => [140.6, 103],
            'OFICINA CENTRAL' => [140.6, 106.5],
            'UNIDAD DE TRANSPARENCIA' => [140.6, 110.6],
            'ZONA CENTRO' => [140.6, 114],
            'ZONA NORESTE' => [140.6, 117.5],
            'ZONA NOROESTE' => [140.6, 120.5],
            'ZONA SURESTE' => [140.6, 124.5],
            'ZONA SUROESTE' => [140.6, 127.8],
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
        $pdf->SetXY(174.5, 41.5); // Posición X, Y en el PDF
        $pdf->Write(0, $fechaActual = now()->format('d/m/Y'));

        //DATA NUM TURNO
        $pdf->SetXY(46, y: 56); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_turno_sistema);

        //DATA NUM DOCUMENTO
        $pdf->SetXY(46, 61.5); // Posición X, Y en el PDF
        $pdf->Write(0, $data->num_documento);


        //DATA FOLIO DE GESTION
        $pdf->SetXY(46, 68.5); // Posición X, Y en el PDF
        $pdf->Write(0, $data->folio_gestion);

        //FECHA DE INICIO
        $pdf->SetXY(176, y: 56); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_inicio);

        //FECHA DE FIN 
        $pdf->SetXY(176, 61.5); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_fin);

        //FECHA DE DOCUMENTO
        $pdf->SetXY(176, 68.5); // Posición X, Y en el PDF
        $pdf->Write(0, $data->fecha_documento);


        //DATA UNIDAD
        $pdf->SetXY(46, 72.7); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->unidad));

        //DATA COORDINACION
        $pdf->SetXY(46, 85); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->coordinacion));

        //DATA AREA
        $pdf->SetFont('Helvetica', '', 8); // Fuente Arial normal
        $pdf->SetXY(46, 91.5); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->area));

        //DATA TRAMITE
        $pdf->SetFont('Helvetica', '', 9); // Fuente Arial normal
        $pdf->SetXY(46, 154); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->tramite));

        //DATA CODIGO
        $pdf->SetXY(46, 160.5); // Posición X, Y en el PDF
        $pdf->Write(0, $data->codigo);

        //DATA REMITENTE
        $pdf->SetXY(46, 164.9); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->remitente));

        //DATA PUESTO REMITENTE
        $pdf->SetXY(46, 171.7); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->puesto_remitente));


        //DATA ASUNTO
        $pdf->SetXY(46, 182); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->asunto));

        //DATA LUGAR
        $pdf->SetXY(46, 205); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->entidad));

        //DATA OBSERVACIONES
        $pdf->SetXY(46, 212.6); // Posición X, Y en el PDF
        $pdf->MultiCell(0, 4, utf8_decode($data->observaciones));

        //DATA USUARIO
        $pdf->SetXY(46, 233); // Posición X, Y en el PDF
        $pdf->Write(0, utf8_decode($data->user_area));


        // Enviar el PDF generado al navegador
        return response($pdf->Output('I'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="pdf-modificado.pdf"');
    }
}
