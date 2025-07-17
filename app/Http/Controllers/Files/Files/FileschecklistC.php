<?php

namespace App\Http\Controllers\Files\Files;

use App\Http\Controllers\Controller;
use App\Models\Files\Files\FileschecklistM;
use Illuminate\Http\Request;

class FileschecklistC extends Controller
{
    public function view($id)
    {
        $model = new FileschecklistM();
        $empleado = $model->obtenerEmpleadoPorId($id);

        if (!$empleado) {
            abort(404, 'Empleado no encontrado');
        }

        $insertado = $model->insertarGestionDocumentoPorEmpleado($id);

        if ($insertado === false) {
            // Opcional: aquí puedes hacer algo si ya existía el registro, 
            // como registrar un log o mostrar mensaje
            // Log::info("El empleado $id ya tiene un registro en tbl_gestion_documentos");
        }

        return view('files.fileschecklist.list', [
            'id' => $id,
            'empleado' => $empleado,
            'empleado_json' => json_encode($empleado)
        ]);
    }
}
