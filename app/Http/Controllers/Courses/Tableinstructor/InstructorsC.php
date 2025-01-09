<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Instructor;

class InstructorsC extends Controller
{
    // Mostrar la lista principal de instructores
    public function list()
    {
        return view('instructors.list');
    }

    // Método para obtener la tabla (por ejemplo, para Datatables)
    public function table(Request $request)
    {
        try {
            $iterator = $request->input('iterator'); // Desplazamiento para paginación
            $searchValue = $request->input('searchValue'); // Valor de búsqueda

            // Consulta básica con filtros de búsqueda
            $query = Instructor::query();
            if ($searchValue) {
                $query->where('uuid_cv', 'like', '%' . $searchValue . '%')
                      ->orWhere('uuid_constancia', 'like', '%' . $searchValue . '%');
            }

            // Obtener resultados con paginación
            $instructores = $query->offset($iterator)->limit(10)->get();

            return response()->json([
                'data' => $instructores,
                'status' => true,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('instructors.create');
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $instructor = Instructor::find($id);
        if (!$instructor) {
            return redirect()->route('tableinstructor.list')->with('error', 'Instructor no encontrado');
        }

        return view('instructors.edit', compact('instructor'));
    }

    // Guardar un nuevo instructor o actualizar uno existente
    public function save(Request $request)
    {
        $validated = $request->validate([
            'id_empleados' => 'required|integer',
            'uuid_constancia' => 'nullable|string',
            'uuid_cv' => 'nullable|string',
            'estatus_apto' => 'nullable|integer',
            'estatus_instructor' => 'nullable|integer',
            'id_usuario_sistema' => 'nullable|integer',
            'fecha_usuario' => 'nullable|date',
        ]);

        $data = array_merge($validated, [
            'id_usuario_sistema' => Auth::id(),
            'fecha_usuario' => Carbon::now(),
        ]);

        if ($request->has('id_instructor')) {
            // Actualización
            Instructor::where('id_instructor', $request->id_instructor)->update($data);
            return redirect()->route('tableinstructor.list')->with('success', 'Instructor actualizado correctamente.');
        } else {
            // Creación
            Instructor::create($data);
            return redirect()->route('tableinstructor.list')->with('success', 'Instructor creado correctamente.');
        }
    }

    // Eliminar un instructor
    public function destroy($id)
    {
        Instructor::where('id_instructor', $id)->delete();
        return response()->json(['success' => 'Instructor eliminado']);
    }

    // Método adicional para gestionar datos en la nube
    public function cloud($id)
    {
        $instructor = Instructor::find($id);
        if (!$instructor) {
            return redirect()->route('tableinstructor.list')->with('error', 'Instructor no encontrado');
        }

        // Aquí puedes implementar lógica para interactuar con Alfresco o datos en la nube
        return view('instructors.cloud', compact('instructor'));
    }
}