<?php

namespace App\Http\Controllers\Courses\Assignedcourse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Admin\MessagesC;
use App\Models\Courses\Courses\Assignedcourse\AssignedcourseM;

class AssignedcourseC extends Controller
{
    public function list()
    {
        try {
            $assignedCourses = AssignedcourseM::all();
            return view('courses.assignedcourse.list', compact('assignedCourses'));
        } catch (\Exception $e) {
            Log::error('❌ Error al obtener la lista de cursos asignados: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'No se pudo obtener la lista de cursos.');
        }
    }

    public function save(Request $request)
    {
        Log::info('🚀 Entrando en save() con CURP: ' . $request->curp);
        $messagesC = new MessagesC();

        try {
            $request->validate([
                'curp' => 'required|string|size:18',
            ]);

            if ($request->is_editing == 1) {
                return $this->update($request, $request->id);
            }

            $assignedcourseM = new AssignedcourseM();
            $idAlumno = $assignedcourseM->obtenerOcrearUsuarioPorCurp($request->curp);


            if (!$idAlumno) {
                return $messagesC->messageErrorRedirect('assignedcourse.list', 'Error al registrar alumno.');
            }

            return $messagesC->messageSuccessRedirect('assignedcourse.list', 'Alumno registrado correctamente.');
        } catch (\Exception $e) {
            Log::error('🔥 Error en save(): ' . $e->getMessage());
            return $messagesC->messageErrorRedirect('assignedcourse.list', 'Error en el servidor.');
        }
    }

    public function searchTable(Request $request)
{
    try {
        $iterator = $request->input('iterator', 1);
        $searchValue = $request->input('searchValue',''); // 🔹 Acepta nulos

        // 🔹 Validación mejorada
        $request->validate([
            'iterator' => 'required|integer|min:1',
            'searchValue' => 'nullable|string|max:255',
        ]);

        // Obtener resultados paginados
        $assignedcourseM = new AssignedcourseM();
        $courses = $assignedcourseM->list($iterator, $searchValue);

        return response()->json([
            'status' => true,
            'message' => 'Resultados obtenidos correctamente',
            'data' => $courses->items(),
            'pagination' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
            ],
        ], 200);
    } catch (\Exception $e) {
        Log::error('❌ Error en searchTable(): ' . $e->getMessage());
        return response()->json([
            'status' => false,
            'message' => 'Error en el servidor.',
            'error' => $e->getMessage(),
        ], 500);
    }
}



public function dataCurp(Request $request)
{
    try {
        $request->validate([
            'curp' => 'required|string|size:18',
        ]);

        Log::info("🔎 CURP recibida: " . $request->curp);
        $assignedcourseM = new AssignedcourseM();

        $centralCurp = $assignedcourseM->centralCurp($request->curp);
        $empleadoHRAES = $assignedcourseM->buscarEmpleadoHRAES($request->curp);
        $empleadoTransferidos = $assignedcourseM->buscarEmpleadoTransferidos($request->curp);

        $resultado = $centralCurp ?? $empleadoHRAES ?? $empleadoTransferidos;

        return response()->json([
            'status' => (bool) $resultado,
            'value' => $resultado ?? (object) [], // 🔹 Evita errores en el frontend
        ], 200);
    } catch (\Exception $e) {
        Log::error('❌ Error en dataCurp(): ' . $e->getMessage());
        return response()->json([
            'status' => false,
            'message' => 'Error en el servidor.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    public function create()
    {
        return view('courses.assignedcourse.form', [
            'item' => new AssignedcourseM(),
            'curp' => '',
            'nombre' => '_',
            'primer_apellido' => '_',
            'segundo_apellido' => '_',
            'rfc' => '_'
        ]);
    }

    public function edit($id)
    {
        try {
            $assignedcourseM = new AssignedcourseM();
            $item = $assignedcourseM->editAssignedCourse($id);

            if (!$item) {
                return redirect()->route('assignedcourse.list')->with('error', 'Alumno no encontrado.');
            }

            Log::info("✅ Datos enviados a la vista: ", (array) $item);

            return view('courses.assignedcourse.form', [
                'item' => $item,
                'curp' => $item->curp ?? '',
                'nombre' => $item->nombre ?? '_',
                'primer_apellido' => $item->primer_apellido ?? '_',
                'segundo_apellido' => $item->segundo_apellido ?? '_',
                'rfc' => $item->rfc ?? '_'
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error en edit(): ' . $e->getMessage());
            return redirect()->route('assignedcourse.list')->with('error', 'Error en el servidor.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            Log::info('🔄 Datos recibidos en update():', $request->all());

            $request->validate([
                'curp' => 'required|string|size:18',
                'estatus' => 'required|in:0,1',
            ]);

            $assignedcourseM = new AssignedcourseM();
            $updated = $assignedcourseM->updateAssignedCourse($id, [
                'curp' => strtoupper($request->curp),
                'estatus' => $request->estatus
            ]);

            if (!$updated) {
                return redirect()->route('assignedcourse.list')->with('error', 'No se pudo actualizar el alumno.');
            }

            return redirect()->route('assignedcourse.list')->with('success', 'Alumno actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error('🔥 Error en update(): ' . $e->getMessage());
            return redirect()->route('assignedcourse.list')->with('error', 'Error en el servidor.');
        }
    }

    public function cloud($id)
    {
        Log::info("📌 ID recibido en AssignedcourseC@cloud():", ['id' => $id]);

        return view('courses.assignedcourse.cloud', [
            'idAlumno' => $id
        ]);
    }

    public function delete(Request $request)
    {
        try {
            $id = $request->id;
            $assignedcourseM = new AssignedcourseM();
            $response = $assignedcourseM->deleteAssignedCourseById($id);

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('🔥 Error en delete(): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar alumno: ' . $e->getMessage()
            ], 500);
        }
    }
}