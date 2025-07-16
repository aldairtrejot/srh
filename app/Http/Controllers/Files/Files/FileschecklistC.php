<?php

namespace App\Http\Controllers\Files\Files;

use App\Http\Controllers\Controller;
use App\Models\Files\Files\FileschecklistM;
use App\Models\Files\Files\FilesdocumentM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class FileschecklistC extends Controller
{
  public function view($id)
{
      $model = new FilesChecklistM();
      $empleado = $model->obtenerEmpleadoPorId($id);

    if (!$empleado) {
        abort(404, 'Empleado no encontrado');
    }
    // Obtener catálogo de documentos
    $catalogoModel = new FilesdocumentM();
    $documentos = $catalogoModel->listdocuments();

    return view('files.fileschecklist.list', [
        'id' => $id,
        'empleado' => $empleado,
        'empleado_json' => json_encode($empleado),// lo convertimos a JSON para JavaScript
        'documentos' => $documentos 
    ]);
}


}