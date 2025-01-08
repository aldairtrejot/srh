<?php

namespace App\Http\Controllers\Email;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Letter\Letter\LetterM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailC extends Controller
{

    // La funcion manda correo para correspondecia
    public function emailLetter(Request $request)
    {
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
                $message->from('soporterh.imssbienestar@gmail.com', 'SIRH') // Agrega el nombre y el correo del remitente
                    ->to('rodolfo.trejo@imssbienestar.gob.mx')
                    ->subject($subject);
                // Elimina setBody(), Laravel automáticamente maneja el contenido como HTML
            });

            return response()->json([
                'status' => true,
                'value' => 'Correo enviado exitosamente',
            ]);
        } catch (\Exception $e) {
            // Capturar cualquier excepción y mostrar el error
            Log::info($e->getMessage());
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

}

