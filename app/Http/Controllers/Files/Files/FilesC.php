<?php

namespace App\Http\Controllers\Files\Files;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Files\Files\FilesM;

class FilesC extends Controller
{
    public function listview(Request $request)
    {
        return view('files.listview');
    }

    public function buscarEmpleado(Request $request)
    {
        $valor = trim($request->input('datos'));
        $pagina = $request->input('page', 1);

        $model = new FilesM();
        $resultados = $model->buscarEmpleadoHraes($valor, $pagina);

        return response()->json($resultados);
    }
}





