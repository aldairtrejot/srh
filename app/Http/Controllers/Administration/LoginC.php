<?php

namespace App\Http\Controllers\Administration;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Administration\LoginM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Redirect;
use Auth;
class LoginC extends Controller
{
    // Retorna la vista inicial de login
    public function __invoke()
    {
        return view('administration/login');
    }

    public function authenticate(Request $request)
    {
        // Crear objeto del modelo
        $loginM = new LoginM();

        // Data
        $key = 'login-attempts:' . $request->ip();

        //  Primero validar los datos (incluyendo el CAPTCHA)
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            //'captcha' => 'required|captcha' //  Validar solo aquí, no en Auth::attempt()
        ]);

        // Validación de inicio de sesion para max 10 por minuto
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return back()->with([
                'value' => 'error', // VALUE_IS(error, warning, success)
                'message' => 'Demasiados intentos. Intenta en 1 minuto.',
                'estatus' => 'true'
            ]);
        }

        //  Intentar autenticar al usuario (sin incluir el CAPTCHA)
        $credentials = $request->only('email', 'password'); //  Excluir 'captcha'

        if (Auth::attempt($credentials)) {
            // Si la autenticación es exitosa, regeneramos la sesión y redirigimos al dashboard
            Log::info('LOG_LOGIN_RECORD: ', [
                'id_user' => Auth::id(),
                'ip_user' => $request->ip(),
                'time_user' => now(),
            ]);// Log de inicio de sesion

            $request->session()->regenerate();
            $userId = Auth::id();
            $roleUser = $loginM->validate($userId);
            session()->put('SESSION_ROLE_USER', $roleUser);
            return redirect()->intended('dashboard');
        }

        // Redirección a login con mensaje de error
        RateLimiter::hit($key, 60); // Expira en 60 segundos
        return back()->with([
            'value' => 'error', // VALUE_IS(error, warning, success)
            'message' => 'Información de inicio de sesión incorrecta.',
            'estatus' => 'true'
        ]);
    }

    // Cierre de sesión
    public function logout(Request $request, Redirect $redirect)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->forget('SESSION_ROLE_USER');
        return Redirect::to('/login');
    }
}
