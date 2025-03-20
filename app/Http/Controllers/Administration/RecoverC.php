<?php

namespace App\Http\Controllers\Administration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
class RecoverC extends Controller
{
    public function __invoke()
    {
        return view('administration/recover');
    }

    public function updatePassword(Request $request)
    {
        // Data
        $existsEmail = false;
        $key = 'login-attempts:' . $request->ip();

        $request->validate([
            'email' => 'required|email',
            'captcha' => 'required|captcha' 
        ]);

        // Validación intento de recover, max 3 por minuto
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->with([
                'value' => 'error', // VALUE_IS(error, warning, success)
                'message' => 'Demasiados intentos. Intenta en 1 minuto.',
                'estatus' => 'true'
            ]);
        }

        $exists = User::where('email', $request->email)->exists();

        if ($exists) { // Emial exists
            $user = User::where('email', $request->email)->first();
            $newPassword = Str::random(10);
            $user->password = Hash::make($newPassword);
            $user->save();
            $this->sendEmail($request->email, $user->name, $newPassword);
            $existsEmail = true;
        }

        RateLimiter::hit($key, 60); // Expira en 60 segundos
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
        $currentDate = Carbon::now();
        $yearMonthDay = $currentDate->format('d/m/y');  // Año-Mes-Día
        $time = $currentDate->format('H:i');  // Hora:Minutos:Segundos

        // Datos que se pasarán a la vista
        $data = [
            'subject' => $subject,
            'password' => $password,
            'name' => strtoupper($name),
            'fecha' => $yearMonthDay,
            'hora' => $time,
        ];

        // Enviar el correo con la vista Blade
        Mail::send('administration.email.emailPassword', $data, function ($message) use ($subject, $email) {
            $message->from('soporterh.imssbienestar@gmail.com', 'SIRH')  // Dirección del remitente
                ->to($email)  // Dirección del destinatario
                ->subject($subject);  // Asunto del correo
        });
    }


}
