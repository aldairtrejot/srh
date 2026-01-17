<?php

namespace App\Http\Controllers\Courses\Tableinstructor;

use App\Models\CloudCV;
use App\Models\CloudConstancia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CloudtableinsC extends Controller
{
    // Obtener datos del encabezado para la vista de la nube
    public function cloudData(Request $request)
    {
        $id_instructor = $request->id_instructor;

        // Simulación de datos (puedes reemplazar con lógica real)
        $data = [
            'nombre_instructor' => 'Ejemplo Nombre',
            'estatus' => 'Activo',
            'archivos' => 5,
        ];

        return response()->json([
            'value' => $data,
            'status' => true,
        ]);
    }

    // Listar CVs y constancias asociados al instructor
    public function cloudFiles(Request $request)
    {
        $id_instructor = $request->id_instructor;

        $cvs = CloudCV::where('id_instructor', $id_instructor)->get();
        $constancias = CloudConstancia::where('id_instructor', $id_instructor)->get();

        return response()->json([
            'cvs' => $cvs,
            'constancias' => $constancias,
            'status' => true,
        ]);
    }

    // Subir archivos al sistema de la nube (CV o Constancia)
    public function upload(Request $request)
    {
        $now = Carbon::now();
        $status = false;
        $message = 'Archivo subido correctamente.';

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $fileName = strtoupper($request->tipo) . '_' . $file->getClientOriginalName();
            $fileExtension = $file->getClientOriginalExtension();
            $fileSize = $file->getSize() / 1024 / 1024;

            // Validaciones de tamaño y extensión
            $maxSize = 5;
            $validExtensions = ['pdf', 'docx', 'jpg'];

            if ($fileSize > $maxSize) {
                $message = "El archivo supera el tamaño máximo permitido de {$maxSize} MB.";
            } elseif (!in_array($fileExtension, $validExtensions)) {
                $message = "Extensiones permitidas: " . implode(', ', $validExtensions);
            } else {
                // Lógica simulada para subir el archivo a Alfresco
                $result = 'UID_GENERADO_POR_ALFRESCO';

                // Guardar en la base de datos
                $data = [
                    'uid' => $result,
                    'nombre' => $fileName,
                    'estatus' => true,
                    'fecha_usuario' => $now,
                    'id_instructor' => $request->id_instructor,
                    'id_usuario_sistema' => Auth::id(),
                ];

                if ($request->tipo == 'cv') {
                    CloudCV::create($data);
                } else {
                    CloudConstancia::create($data);
                }

                $status = true;
            }
        } else {
            $message = 'No se ha seleccionado un archivo válido.';
        }

        return response()->json([
            'message' => $message,
            'status' => $status,
        ]);
    }

    // Eliminar un archivo del sistema de la nube
    public function delete(Request $request)
    {
        $uid = $request->uid;
        $now = Carbon::now();
        $status = false;

        $data = [
            'estatus' => false,
            'fecha_usuario' => $now,
            'id_usuario_sistema' => Auth::id(),
        ];

        // Buscar y actualizar el estatus en las tablas correspondientes
        $cv = CloudCV::where('uid', $uid)->update($data);
        $constancia = CloudConstancia::where('uid', $uid)->update($data);

        if ($cv || $constancia) {
            $status = true;
        }

        return response()->json([
            'message' => $status ? 'Archivo eliminado correctamente.' : 'No se encontró el archivo.',
            'status' => $status,
        ]);
    }
}
