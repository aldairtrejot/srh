<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

class MessagesC extends Controller
{
    //Retorna un mensage de error
    public function messageErrorBack($text)
    {
        return back()->withInput()->with([
            'message' => $text,
            'value' => 'error',
            'estatus' => 'true'
        ]);
    }

    //Retorna  un mensaje de exito, con la ruta a la que se le va asociar
    public function messageSuccessRedirect($route, $text)
    {
        return redirect()->route($route)->with([
            'value' => 'success', //VALUE_IS(error, warning, success)
            'message' => $text,
            'estatus' => 'true'
        ]);
    }

    public function messageErrorRedirect($route, $message)
{
    return redirect()->route($route)->with('error', $message);
}
}
