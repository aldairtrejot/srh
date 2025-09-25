<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Admin\MessagesC;
use App\Http\Controllers\Controller;
use App\Models\Administration\UserM;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordC extends Controller
{
    //
    public function changePassword()
    {
        $user = Auth::user();

        if ($user->password_update) {
            return redirect()->intended('dashboard');
        } else {
            return view('administration/passwordUpdate');
        }
    }

    public function savePassword(Request $request)
    {
        $user = Auth::user();
        $messagesC = new MessagesC;

        $request->validate([
            'new_value' => [
                'required',
                'string',
                'min:8', // mínimo 8 caracteres
                'regex:/[A-Z]/',      // al menos una mayúscula
                'regex:/[a-z]/',      // al menos una minúscula
                'regex:/[0-9]/',      // al menos un número
                'regex:/[@&.#]/', // al menos un carácter especial
                'same:confirm_password', // valida que coincida con password_confirmation
            ],
            'confirm_password' => [
                'required',
            ],
        ], [
            'new_value.required' => 'El campo es obligatorio.',
            'confirm_password.required' => 'El campo es obligatorio.',
            'new_value.min' => 'El campo debe tener al menos 8 caracteres.',
            'new_value.regex' => 'El campo debe incluir al menos una mayúscula, una minúscula, un número y un caracter especial.',
            'new_value.same' => 'Los campos no coinciden.',
        ]);

        UserM::where('id', Auth::id())->update([
            'password' => Hash::make($request->new_value),
            'password_update' => true,
        ]);

        return $messagesC->messageSuccessRedirect('dashboard', 'Contraseña actualizada.');

    }
}
