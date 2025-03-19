<?php

namespace App\Http\Controllers\Courses\Assignedcourse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        Log::info("📌 Datos recibidos en save():", $request->all());
    
        $messagesC = new MessagesC();
    
        try {
            // 🔹 Removemos la asignación automática de id_cursos
            // 🔹 Ahora será obligatorio en la validación
    
            $request->validate([
                'curp' => 'required|string|size:18',
            ]);
    
            if ($request->is_editing == 1) {
                return $this->update($request, $request->id);
            }
    
            $assignedcourseM = new AssignedcourseM();
            $idAlumno = $assignedcourseM->obtenerAlumno($request->curp, 1, $request->id_cursos);
    
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
    
    public function add($id = null)
    {
        // Buscar el curso si el ID existe, si no, definir $item como null
        $item = $id ? AssignedcourseM::find($id) : null;

        return view('courses.assignedcourse.add', compact('item'));
    }

    
    public function courses($idEmpleadoCursos = null)
    {
        try {
            Log::info("🔎 Buscando cursos para ID: " . ($idEmpleadoCursos ?? 'TODOS'));
    
            $assignedcourseM = new AssignedcourseM();
    
            if ($idEmpleadoCursos) {
                // Buscar los cursos de ese empleado
                $cursos = $assignedcourseM->obtenerCursosConDetallesPorEmpleado($idEmpleadoCursos);
    
                if ($cursos->isEmpty()) {
                    Log::warning("⚠️ No se encontraron cursos para ID: $idEmpleadoCursos");
    
                    // Si la solicitud es AJAX o JSON, devolver JSON
                    if (request()->ajax() || request()->wantsJson()) {
                        return response()->json([
                            'status' => false,
                            'message' => 'No se encontraron cursos asignados',
                            'data' => [],
                        ], 200);
                    }
    
                    // Si la solicitud es desde el navegador (HTML), redirigir con mensaje de error
                    return redirect()->route('assignedcourse.courses')->with('error', 'No se encontraron cursos asignados.');
                }
    
                Log::info("✅ Cursos obtenidos para ID: $idEmpleadoCursos", ['cursos' => $cursos]);
            } else {
                // Obtener todos los cursos si no hay un ID
                $cursos = $assignedcourseM->all();
                Log::info("✅ Todos los cursos obtenidos.", ['cursos' => $cursos]);
            }
    
            // Si la solicitud es AJAX o JSON, devolver JSON
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Cursos obtenidos correctamente',
                    'data' => $cursos,
                ], 200);
            }
    
            // Si la solicitud es desde el navegador, cargar la vista con los cursos
            return view('courses.assignedcourse.courses', compact('cursos'));
    
        } catch (\Exception $e) {
            Log::error("🔥 Error en courses(): " . $e->getMessage());
    
            // Si la solicitud es AJAX o JSON, devolver JSON
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error en el servidor.',
                    'error' => $e->getMessage(),
                ], 500);
            }
    
            // Si la solicitud es desde el navegador, redirigir con mensaje de error
            return redirect()->route('assignedcourse.courses')->with('error', 'Error en el servidor.');
        }
    }
    
}

