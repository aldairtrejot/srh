<?php

namespace App\Http\Controllers\Files\Files;

use App\Http\Controllers\Controller;
use App\Models\Files\Files\FileschecklistM;
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

    return view('files.fileschecklist.list', [
        'id' => $id,
        'empleado' => $empleado,
        'empleado_json' => json_encode($empleado) // 👈 lo convertimos a JSON para JavaScript
    ]);
}


}