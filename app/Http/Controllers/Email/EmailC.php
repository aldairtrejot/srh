<?php

namespace App\Http\Controllers\Email;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Letter\Letter\LetterM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailC extends Controller
{
    public function emailLetter(Request $request)
    {
        // Configuración SMTP utilizando variables directamente
        $smtpConfig = [
            'driver' => 'smtp',
            'host' => 'webmail.imssbienestar.gob.mx',
            'port' => 587,
            'encryption' => null,  // No cifrado SSL/TLS explícito, ya que el servidor SMTP usa STARTTLS.
            'username' => 'soporte_rh@imssbienestar.gob.mx',
            'password' => 'gbchjbdfbqkjpmvl',
            'from_address' => 'soporte_rh@imssbienestar.gob.mx',
            'from_name' => env('APP_NAME', 'Mi Aplicación'),
        ];

        try {
            // Cambiar la configuración SMTP en tiempo de ejecución
            config(['mail.mailers.smtp.host' => $smtpConfig['host']]);
            config(['mail.mailers.smtp.port' => $smtpConfig['port']]);
            config(['mail.mailers.smtp.encryption' => $smtpConfig['encryption']]);
            config(['mail.mailers.smtp.username' => $smtpConfig['username']]);
            config(['mail.mailers.smtp.password' => $smtpConfig['password']]);
            config(['mail.from.address' => $smtpConfig['from_address']]);
            config(['mail.from.name' => $smtpConfig['from_name']]);

            // Enviar un correo de prueba
            Mail::raw('Este es un correo de prueba enviado con la configuración SMTP proporcionada.', function ($message) {
                $message->to('destinatario@ejemplo.com')
                    ->subject('Correo de prueba desde Laravel');
            });

            // Si el correo se envía correctamente, registramos el éxito
            Log::info('Correo de prueba enviado con éxito a destinatario@ejemplo.com');

        } catch (\Exception $e) {
            // Si ocurre un error, lo capturamos y registramos el error
            Log::error('Error al enviar el correo de prueba: ' . $e->getMessage());

        }
    }

}



/*
// Declarar el asunto y cuerpo del correo
$letterM = new LetterM();
$subject = 'No. DE TURNO ASIGNADO PARA CORRESPONDENCIA';
$body = 'Este es el contenido dinámico del correo';
$mailBody = $letterM->mailLetter($request->id);

// Datos que se pasarán a la vista
$data = [
    'subject' => $subject,
    'turno' => $request->value,
    'body' => $body,
    'mailBody' => $mailBody,
    'nameUser' => strtoupper($request->nameUser),
];

try {
    // Enviar el correo con la vista Blade
    Mail::send('letter.mail.mailLetter', $data, function ($message) use ($subject) {
        $message->to('rodolfo.trejo@imssbienestar.gob.mx')
            ->subject($subject);
        // Elimina setBody(), Laravel automáticamente maneja el contenido como HTML
    });

    return response()->json([
        'value' => 'Correo enviado exitosamente',
    ]);
} catch (\Exception $e) {
    // Capturar cualquier excepción y mostrar el error
    Log::info($e->getMessage());
    return response()->json([
        'error' => $e->getMessage(),
    ]);
}
    */