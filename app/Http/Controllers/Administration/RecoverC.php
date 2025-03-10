<?php

namespace App\Http\Controllers\Administration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
class RecoverC extends Controller
{
    public function __invoke()
    {
        return view('administration/recover');
    }

    public function updatePassword(Request $request)
    {
        $existsEmail = false;

        $request->validate([
            //'email' => 'required|email',
            //'captcha' => 'required|captcha' //  Validar solo aquí, no en Auth::attempt()
        ]);

        $exists = User::where('email', $request->email)->exists();

        if ($exists) { // Emial exists
            $user = User::where('email', $request->email)->first();
            $newPassword = Str::random(10);
            $user->password = Hash::make($newPassword);
            $user->save();
            $this->sendEmail('rodolfo.trejo@imssbienestar.gob.mx', $user->name, $newPassword);
            $existsEmail = true;
        }

        return redirect()->route('result')
            ->with([
                'isUpdatePassword' => true,
                'email' => $request->email,
                'existsEmail' => $existsEmail
            ]);
    }

    private function sendEmail($email, $name, $password)
    {
        $subject = 'ACTUALIZACIÓN DE CONTRASEÑA';

        // Datos que se pasarán a la vista
        $data = [
            'subject' => $subject,
            'password' => $password,
            'name' => $name,
        ];

        // Enviar el correo con la vista Blade
        Mail::send('administration.email.emailPassword', $data, function ($message) use ($subject, $email) {
            $message->from('soporterh.imssbienestar@gmail.com', 'SIRH')  // Dirección del remitente
                ->to($email)  // Dirección del destinatario
                ->subject($subject);  // Asunto del correo
        });
    }


}
