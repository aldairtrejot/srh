<?php

namespace App\Http\Controllers\Courses\Tableinstructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Courses\Courses\Instructores\Instructores\InstructorM// Modelo correcto

class InstructorsC extends Controller
{
    // Mostrar la lista principal de instructores
    public function list()
    {
        return view('courses.tableinstructor.list');
    }

    // Método para obtener la tabla (por ejemplo, para Datatables)
    public function table(Request $request)
    {
        try {
            $iterator = $request->input('iterator', 0); // Desplazamiento para paginación, por defecto 0
            $searchValue = $request->input('searchValue', ''); // Valor de búsqueda, por defecto vacío

            // Consulta básica con filtros de búsqueda
            $query = InstructorM::query();
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
                'message' => 'Error al cargar la tabla: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Mostrar formulario de creación
    public function create()
    {
        $instructor = null;
        return view('courses.tableinstructor.form', compact('instructor'));
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $instructor = InstructorM::find($id);
        if (!$instructor) {
            return redirect()->route('tableinstructor.list')->with('error', 'Instructor no encontrado');
        }

        return view('courses.tableinstructor.form', compact('instructor'));
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
        ]);

        try {
            $data = array_merge($validated, [
                'id_usuario_sistema' => Auth::id(),
                'fecha_usuario' => Carbon::now(),
            ]);

            if ($request->has('id_instructor')) {
                // Actualización
                $instructor = InstructorM::find($request->id_instructor);
                if (!$instructor) {
                    return redirect()->route('tableinstructor.list')->with('error', 'Instructor no encontrado para actualizar.');
                }

                $instructor->update($data);
                return redirect()->route('tableinstructor.list')->with('success', 'Instructor actualizado correctamente.');
            } else {
                // Creación
                InstructorM::create($data);
                return redirect()->route('tableinstructor.list')->with('success', 'Instructor creado correctamente.');
            }
        } catch (\Exception $e) {
            return redirect()->route('tableinstructor.list')->with('error', 'Error al guardar el instructor: ' . $e->getMessage());
        }
    }

    // Eliminar un instructor
    public function destroy($id)
    {
        try {
            $instructor = InstructorM::find($id);
            if (!$instructor) {
                return response()->json(['success' => false, 'message' => 'Instructor no encontrado'], 404);
            }

            $instructor->delete();
            return response()->json(['success' => true, 'message' => 'Instructor eliminado']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el instructor: ' . $e->getMessage()], 500);
        }
    }

    // Método adicional para gestionar datos en la nube
    public function cloud($id)
    {
        $instructor = InstructorM::find($id);
        if (!$instructor) {
            return redirect()->route('tableinstructor.list')->with('error', 'Instructor no encontrado');
        }

        return view('courses.tableinstructor.cloud', compact('instructor'));
    }
}
