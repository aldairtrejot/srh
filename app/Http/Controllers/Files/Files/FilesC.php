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
    $model = new FilesM();

    $curp = $valor;
    $rfc = $valor;
    $nombre = $valor;
    $primerApellido = $valor;
    $segundoApellido = $valor;

    $resultados = $model->buscarEmpleadoHraes($valor);
return response()->json($resultados);

}

}




