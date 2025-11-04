<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Models\Letter\Letter\LetterM;
use App\Models\Letter\Office\AnexosM;
use App\Models\Letter\Office\OfficeM;
use App\Models\Letter\Office\OficionM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmailC extends Controller
{
    // La funcion manda correo para correspondecia
    public function emailLetter(Request $request)
    {
        try {

            // validadion de roles
            // si no return f

            // Declarar el asunto y cuerpo del correo
            $letterM = new LetterM;
            $subject = 'Folio rechazado';
            $body = 'Este es el contenido dinámico del correo';
            $mailBody = $letterM->mailLetter($request->id);

            // Datos que se pasarán a la vista
            $data = [
                'subject' => $subject,
                'turno' => $request->value,
                'body' => $body,
                'mailBody' => $mailBody,
                'nameUser' => strtoupper($request->nameUser),
                'observaciones' => strtoupper($request->observaciones),
            ];

            // Enviar el correo con la vista Blade
            Mail::send('letter.mail.mailLetter', $data, function ($message) use ($subject, $request) {
                $message->from('soporterh.imssbienestar@gmail.com', 'SIRH')  // Dirección del remitente
                    ->to($request->mail)  // Dirección del destinatario
                    ->subject($subject);  // Asunto del correo
            });

            // ACTUALIZAR ESTATUS
            LetterM::where('id_tbl_correspondencia', $request->id)
                ->update(['id_cat_estatus' => 1]);

            // Obtener id de oficio
            $oficio = OfficeM::where('id_tbl_correspondencia', $request->id)->first();

            // Eliminar registros relacionados primero
            if (! empty($oficio->id_tbl_oficio)) {
                OficionM::where('id_tbl_oficio', $oficio->id_tbl_oficio)->delete();
                AnexosM::where('id_tbl_oficio', $oficio->id_tbl_oficio)->delete();

                // Eliminar el oficio principal
                OfficeM::where('id_tbl_oficio', $oficio->id_tbl_oficio)->delete();
            }

            return response()->json([
                'status' => true,
                'value' => 'Correo enviado exitosamente',
            ]);
        } catch (\Exception $e) {
            // Capturar cualquier excepción y mostrar el error
            // \Log::info('erro: '.$e);

            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function main(Request $request)
    {
        try {
           if (
    ! in_array(1, session('SESSION_ROLE_USER', [])) &&
    ! in_array(2, session('SESSION_ROLE_USER', [])) &&
    ! in_array(15, session('SESSION_ROLE_USER', []))
) {
    return response()->json([
        'status' => false,
    ]);
}
            $query = DB::table('correspondencia.tbl_correspondencia as c')
                ->join('administration.users as u', 'u.id', '=', 'c.id_usuario_enlace')
                ->select('u.name', 'u.email')
                ->where('c.id_tbl_correspondencia', $request->id)
                ->first();

            $name = $query->name ?? null;
            $email = $query->email ?? null;

            return response()->json([
                'status' => true,
                'name' => $name,
                'email' => $email,
            ]);
        } catch (\Throwable $th) {
            // Capturar cualquier excepción y mostrar el error
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
            ]);
        }
    }
}
