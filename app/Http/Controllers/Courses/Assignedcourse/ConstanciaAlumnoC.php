<?php

namespace App\Http\Controllers\Courses\Assignedcourse;

use App\Http\Controllers\Controller;
use App\Http\Controllers\QR\QrCodeController;
use App\Models\Courses\Courses\Assignedcourse\AssignedcourseM;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;


class ConstanciaAlumnoC extends Controller
{
    public function generatePdf($id)
{
    $AssignedcourseM = new AssignedcourseM();  
    $data = $AssignedcourseM->getDataReport($id);

    if (!$data) {
        return response()->json(['status' => false, 'message' => 'No se encontró información del curso.'], 404);
    }
    
    $fechaActual = Carbon::now()->locale('es')->isoFormat('LL'); 
    $textoFecha = "Ciudad de México a " . $fechaActual;

    $pdfPath = public_path('assets/documents/template-pdf/templateConstanciaCurso.pdf'); 
    
    $pdf = new Fpdi();
    $pdf->SetAutoPageBreak(false);
    $pdf->SetMargins(0, 0, 0);
    $pdf->setSourceFile($pdfPath);
    $template = $pdf->importPage(1);
    $pdf->addPage();
    $pdf->useTemplate($template, 0, 0, 210, 297);

    $pdf->SetFont('Arial', 'B', 30);
    $pdf->SetTextColor(181, 139, 91);
    $pdf->SetXY(0, 115);
    $pdf->Cell(170, 15, utf8_decode($data->nombre), 0, 1, 'C');
    $pdf->SetXY(0, 129);
    $pdf->Cell(170, 15, utf8_decode($data->primer_apellido . ' ' . $data->segundo_apellido), 0, 1, 'C');

    $pdf->SetFont('Arial', '', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetXY(20, 282);
    $pdf->Cell(210, 10, utf8_decode($textoFecha), 0, 0, 'C'); 

    return response()->stream(function () use ($pdf) {
        $pdf->Output('I', 'Constancia.pdf');
    }, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="Constancia.pdf"'
    ]);
}

    
}
