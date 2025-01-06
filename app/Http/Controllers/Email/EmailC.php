<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
class EmailC extends Controller
{
    public function emailLetter(Request $request)
    {
        // Declarar el asunto y cuerpo del correo
        $subject = 'Asunto dinámico';
        $body = 'Este es el contenido dinámico del correo';

        try {
            // Intentar enviar el correo usando Mail::raw
            Mail::raw($body, function ($message) use ($subject) {
                $message->to('rodolfo.trejo@imssbienestar.gob.mx')
                    ->subject($subject);
            });

            return response()->json([
                'value' => 'Correo enviado exitosamente',
            ]);
        } catch (\Exception $e) {
            // Capturar cualquier excepción y mostrar el error
            return response()->json([
                'error' => 'Error al enviar el correo: ' . $e->getMessage(),
            ]);
        }
    }
}
