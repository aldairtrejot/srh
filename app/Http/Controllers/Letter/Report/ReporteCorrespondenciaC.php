<?php

namespace App\Http\Controllers\Letter\Report;

use App\Http\Controllers\Controller;
use App\Models\Letter\Collection\CollectionAreaM;
use App\Models\Letter\Letter\LetterM;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;

class ReporteCorrespondenciaC extends Controller
{
    public function generatePdf($id)
    {
        $LetterM          = new LetterM();
        $collectionAreaM  = new CollectionAreaM();

        // Datos principales del oficio
        $data = $LetterM->getDataReport($id);
        if (!$data) {
            abort(404, 'No se encontró la correspondencia.');
        }

        // Áreas a las que se envió COPIA (array de descripciones)
        $copy = $collectionAreaM->getDataCopia($id); // ['DIVISIÓN DE INGRESOS', 'ZONA SURESTE', ...]

        // Plantilla
        $pdfPath = public_path('assets/documents/template-pdf/templateCorrespondencia.pdf');

        $pdf = new Fpdi();
        $pdf->setSourceFile($pdfPath);
        $template = $pdf->importPage(1);
        $pdf->AddPage();
        $pdf->useTemplate($template);

        // Configuración básica de fuente / color
        $pdf->SetFont('Helvetica', '', 9);
        $pdf->SetTextColor(0, 0, 0);

        // ========================= CABECERA (fecha emisión) =========================
        $fechaActual = Carbon::now()->format('d/m/Y');
        $pdf->SetXY(174.5, 41.5);
        $pdf->Write(0, $fechaActual);

        // ========================= CAMPOS DE LA PLANTILLA =========================

        // No. Turno
        $pdf->SetXY(46, 56);
        $pdf->Write(0, $data->num_turno_sistema);

        // No. Doc
        $pdf->SetXY(46, 61.5);
        $pdf->Write(0, $data->num_documento);

        // Fol. Gestión
        $pdf->SetXY(46, 68.5);
        $pdf->Write(0, $data->folio_gestion);

        // Fecha inicio
        $pdf->SetXY(176, 56);
        $pdf->Write(0, $data->fecha_inicio);

        // Fecha fin
        $pdf->SetXY(176, 61.5);
        $pdf->Write(0, $data->fecha_fin);

        // Fecha doc.
        $pdf->SetXY(176, 68.5);
        $pdf->Write(0, $data->fecha_documento);

        // Unidad
        $pdf->SetXY(46, 72.7);
        $pdf->MultiCell(0, 4, utf8_decode($data->unidad));

        // Coordinación
        $pdf->SetXY(46, 85);
        $pdf->Write(0, utf8_decode($data->coordinacion));

        // Área / Zona (Área principal)
        $pdf->SetXY(46, 91.5);
        $pdf->Write(0, utf8_decode($data->area));

        // C.R.H.T. (area_2 en la plantilla original)
        $pdf->SetXY(46, 98);
        $pdf->Write(0, utf8_decode($data->area_2));

        // C.R.H. (area_1 en la plantilla original)
        $pdf->SetXY(46, 105.3);
        $pdf->Write(0, utf8_decode($data->area_1));

        // Trámite
        $pdf->SetXY(46, 111);
        $pdf->MultiCell(0, 4, utf8_decode($data->tramite));

        // Clave
        $pdf->SetXY(46, 122.5);
        $pdf->MultiCell(0, 4, utf8_decode($data->codigo));

        // Remitente
        $pdf->SetXY(46, 132);
        $pdf->MultiCell(0, 4, utf8_decode($data->remitente));

        // Puesto remitente
        $pdf->SetXY(46, 140);
        $pdf->MultiCell(0, 4, utf8_decode($data->puesto_remitente));

        // Asunto
        $pdf->SetXY(46, 148);
        $pdf->MultiCell(0, 4, utf8_decode($data->asunto));

        // Lugar
        $pdf->SetXY(46, 172);
        $pdf->MultiCell(0, 4, utf8_decode($data->entidad));

        // Observaciones
        $pdf->SetXY(46, 178);
        $pdf->MultiCell(0, 4, utf8_decode($data->observaciones));

        // Usuario
        $pdf->SetXY(46, 200);
        $pdf->Write(0, utf8_decode($data->user_area));

        // ========================= COPIA A (nuevo renglón) =========================
        // En la plantilla nueva hay un renglón "Copia a" debajo de Usuario.
        // Aquí imprimimos solo las áreas seleccionadas en ctrl_transcribir_correspondencia.
        if (!empty($copy)) {
            // Ajusta la coordenada Y si en tu PDF el texto queda desfasado.
            $pdf->SetXY(46, 205); // <-- esta Y corresponde al recuadro gris de "Copia a"
            
            // Cada área en una línea
            $textoCopias = utf8_decode(implode("\n", $copy));
            $pdf->MultiCell(0, 4, $textoCopias);
        }

        // ========================= RESPUESTA =========================
        return response($pdf->Output('I'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="pdf-modificado.pdf"');
    }
}
