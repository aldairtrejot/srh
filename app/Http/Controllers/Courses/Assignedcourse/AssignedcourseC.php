<?php

namespace App\Http\Controllers\Courses\Assignedcourse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\MessagesC;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\Courses\Courses\Assignedcourse\AssignedcourseM;

class AssignedcourseC extends Controller
{
    public function list()
    {
        try {
            $assignedCourses = AssignedcourseM::all();
            return view('courses.assignedcourse.list', compact('assignedCourses'));
        } catch (\Exception $e) {
            
            
            return redirect()->route('dashboard')->with('error', 'No se pudo obtener la lista de cursos.');
        }
    }

    public function save(Request $request)
{
    $messagesC = new MessagesC();

    try {
        $request->validate([
            'curp' => 'required|string|size:18',
        ]);

        if ($request->is_editing == 1) {
            return $this->update($request, $request->id);
        }

        $assignedcourseM = new AssignedcourseM();
        $idAlumno = $assignedcourseM->obtenerAlumno($request->curp, 1, $request->id_cursos);

        if (!$idAlumno) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error al registrar alumno.'
                ], 400);
            }
            return $messagesC->messageErrorRedirect('assignedcourse.list', 'Error al registrar alumno.');
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'Alumno registrado correctamente.'
            ]);
        }

        return $messagesC->messageSuccessRedirect('assignedcourse.list', 'Alumno registrado correctamente.');

    } catch (\Exception $e) {
        if ($request->ajax()) {
            return response()->json([
                'status' => false,
                'message' => 'Error en el servidor.'
            ], 500);
        }

        return $messagesC->messageErrorRedirect('assignedcourse.list', 'Error en el servidor.');
    }
}

    
public function searchTable(Request $request)
{
    try {
        $iterator = $request->input('iterator', 1);
        $searchValue = $request->input('searchValue', '');

        $request->validate([
            'iterator' => 'required|integer|min:1',
            'searchValue' => 'nullable|string|max:255',
        ]); 

        $assignedcourseM = new AssignedcourseM();
        $courses = $assignedcourseM->list($iterator, $searchValue);

        // 🔹 Llamar a la nueva función para obtener detalles del curso
        foreach ($courses as $course) {
            $detallesCurso = $assignedcourseM->obtenerCursosConDetallesPorEmpleado($course->id_empleado_cursos);
            $course->programa_proyecto = $detallesCurso->isNotEmpty() ? $detallesCurso->first()->programa_proyecto : '-';
            $course->fecha_inicio = $detallesCurso->isNotEmpty() ? $detallesCurso->first()->fecha_inicio : '-';
            $course->fecha_fin = $detallesCurso->isNotEmpty() ? $detallesCurso->first()->fecha_fin : '-';
            $course->horas = $detallesCurso->isNotEmpty() ? $detallesCurso->first()->horas : '-';
            $course->tipo_curso = $detallesCurso->isNotEmpty() ? $detallesCurso->first()->tipo_curso : '-';
            $course->estatus = $detallesCurso->isNotEmpty() ? ($detallesCurso->first()->estatus ? 'ACTIVO' : 'INACTIVO') : '-';
        }

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
        return response()->json([
            'status' => false,
            'message' => 'Error en el servidor.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function create()
{
    $cursos = DB::table('capacitacion.tbl_cursos')->get(); // 🔹 Se obtienen los cursos

    return view('courses.assignedcourse.form', [
        'item' => new AssignedcourseM(),
        'curp' => '',
        'nombre' => '_',
        'primer_apellido' => '_',
        'segundo_apellido' => '_',
        'rfc' => '_',
        'cursos' => $cursos, // 🔹 Enviamos cursos a la vista
    ]);
}

    public function update(Request $request, $id)
    {
        try {
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
            return redirect()->route('assignedcourse.list')->with('error', 'Error en el servidor.');
        }
    }
    
    public function add($id = null)
    {
        // Buscar el curso si el ID existe, si no, definir $item como null
        $item = $id ? AssignedcourseM::find($id) : null;

        return view('courses.assignedcourse.add', compact('item'));
        
    }
    public function courses(Request $request, $idEmpleadoCursos)
    {
        try {
            if (!$idEmpleadoCursos) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontró el ID del empleado.',
                    'data' => []
                ], 400);
            }
    
            $assignedcourseM = new AssignedcourseM();
            $cursos = $assignedcourseM->obtenerCursosConDetallesPorEmpleado($idEmpleadoCursos);
    
            // Si no hay cursos, enviamos un array vacío para que la vista pueda mostrar el mensaje
            if ($cursos->isEmpty()) {
                $cursos = []; // Convertimos a un array vacío para evitar errores en la vista
            }

            // Si la solicitud es AJAX, devolver JSON; de lo contrario, devolver la vista
            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => count($cursos) > 0 ? 'Cursos obtenidos correctamente' : 'No se encontraron cursos asignados',
                    'data' => $cursos
                ], 200);
            }
    
            return view('courses.assignedcourse.courses', compact('idEmpleadoCursos', 'cursos'));
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error en el servidor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function modalCarga()
    {
        // Pasar los cursos a la vista
        return view('courses.assignedcourse.modal');
    }

public function handleMassiveUpload(Request $request)
{
    try {
        $file = $request->file('file');

        if (!$file) {
            return response()->json(['message' => 'No se envió ningún archivo.'], 400);
        }

        $rows = (new FastExcel)->import($file); // <-- esto ya funciona con FastExcel

        $assignedcourseM = new AssignedcourseM();
        $responseData = $assignedcourseM->guardarTemporalUsuariosDesdeFastExcel($rows); // <-- aquí

        return response()->json([
            'status' => true,
            'message' => 'Carga temporal completada.',
            'data' => $responseData
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Ocurrió un error al procesar el archivo.',
            'error' => $e->getMessage()
        ], 500);
    }
}


}    

