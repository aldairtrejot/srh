<?php

namespace App\Http\Controllers\Courses\Tableinstructor;
use App\Http\Controllers\Controller;
use App\Http\Controllers\QR\QrCodeController;
use App\Models\Courses\Courses\Instructores\Instructores\InstructorM;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;

class ReporteConstanciaC extends Controller
{
    public function generatePdf($id)
    {
        $InstructorM = new InstructorM();  
        $data = $InstructorM->getDataReport($id);

        $fechaActual = Carbon::now()->locale('es')->isoFormat('LL'); 
        $textoFecha = "Ciudad de México a " . $fechaActual;

        // Ruta del archivo PDF base
        $pdfPath = public_path('assets/documents/template-pdf/templateConstancia.pdf'); 
        
        // Instancia de FPDI (requiere TCPDF o FPDF)
        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false); // 🔹 Evita saltos automáticos de página
        $pdf->SetMargins(0, 0, 0); // 🔹 Elimina márgenes internos
        $pdf->setSourceFile($pdfPath);
        $template = $pdf->importPage(1);
        $pdf->addPage();
        $pdf->useTemplate($template, 0, 0, 210, 297); // 🔹 Asegura tamaño exacto A4
        
        $fechaActual = Carbon::now(); // Fecha actual

        // 📌 **Configuración del Nombre**
        $pdf->SetFont('Arial', 'B', 30); // Fuente grande y negrita
        $pdf->SetTextColor(181, 139, 91); // Color dorado (RGB: 181, 139, 91)

        // 📌 Posición del Nombre (ARRIBA)
        $pdf->SetXY(0, 115); // 🔹 Centrar manualmente
        $pdf->Cell(170, 15, utf8_decode($data->nombre), 0, 1, 'C');

        // 📌 Posición de los Apellidos (ABAJO)
        $pdf->SetXY(0, 129); // 🔹 Ajuste preciso para evitar traslape
        $pdf->Cell(170, 15, utf8_decode($data->primer_apellido . ' ' . $data->segundo_apellido), 0, 1, 'C');

        // 🔹 **Eliminar espacio en blanco en la parte inferior**
        $pdf->SetY(270); // Mueve el cursor al final del documento
        $pdf->Cell(0, 0, '', 0, 0, 'C'); // Esto empuja la firma hacia arriba

        $pdf->SetFont('Arial', '', 12); // Fuente normal
        $pdf->SetTextColor(0, 0, 0); // Color negro

        $pdf->SetXY(20, 282); // Ajusta según el diseño
        $pdf->Cell(210, 10, utf8_decode($textoFecha), 0, 0, 'C'); 

        // 📌 **Generar Código QR usando el controlador**
        $qrController = new QrCodeController();
        $qrData = "Nombre: {$data->nombre} {$data->primer_apellido} {$data->segundo_apellido}\n";
        $qrData .= "CURP: {$data->curp}\n";
        $qrData .= "Email: {$data->email}";

        $qrPath = $qrController->generateQrCode($qrData);

        // 📌 **Insertar QR en el PDF**
        $pdf->Image($qrPath, 140, 5, 25, 25); // Ajusta X, Y, Ancho y Alto

        // Enviar el PDF generado al navegador
        return response($pdf->Output('I'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="pdf-modificado.pdf"');
    }
}
