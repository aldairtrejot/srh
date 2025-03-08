<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Administration\LoginM;
use Auth;
use Illuminate\Http\Request;
use Redirect;
use Illuminate\Support\Facades\DB;
class LoginC extends Controller
{
    public function __invoke()
    {
        return view('administration/login');
    }

    public function authenticate(Request $request)
    {
        // Crear objeto del modelo
        $loginM = new LoginM();

        //  Primero validar los datos (incluyendo el CAPTCHA)
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required|captcha' //  Validar solo aquí, no en Auth::attempt()
        ]);

        //  Intentar autenticar al usuario (sin incluir el CAPTCHA)
        $credentials = $request->only('email', 'password'); //  Excluir 'captcha'

        if (Auth::attempt($credentials)) {
            // Si la autenticación es exitosa, regeneramos la sesión y redirigimos al dashboard
            $request->session()->regenerate();
            $userId = Auth::id();
            $roleUser = $loginM->validate($userId);
            session()->put('SESSION_ROLE_USER', $roleUser);
            return redirect()->intended('dashboard');
        }

        // Redirección a login con mensaje de error
        return back()->with([
            'value' => 'error', // VALUE_IS(error, warning, success)
            'message' => 'Información de inicio de sesión incorrecta.',
            'estatus' => 'true'
        ]);
    }

    public function logout(Request $request, Redirect $redirect)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->forget('SESSION_ROLE_USER');
        return Redirect::to('/login');
    }
}
