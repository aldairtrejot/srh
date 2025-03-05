<?php

namespace App\Http\Controllers\QR;

use App\Http\Controllers\Controller;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Label\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QrCodeController extends Controller
{
    public function generateQrCode($data)
    {
        // 📌 Crear el código QR
        $qrCode = new QrCode($data);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // 📌 Guardar el QR en un archivo temporal
        $qrPath = storage_path('app/public/temp_qr.png');
        file_put_contents($qrPath, $result->getString());

        return $qrPath; // Retorna la ruta del archivo QR
    }
}

