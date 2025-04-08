<?php

namespace App\Http\Controllers\Courses\Assignedcourse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\MessagesC;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\Courses\Courses\Assignedcourse\AssignedcourseM;

class AssignedcourseC extends Controller
{

    // se encarga de la vista del list
    public function list()
    {
        try {
            $assignedCourses = AssignedcourseM::all();
            return view('courses.assignedcourse.list', compact('assignedCourses'));
        } catch (\Exception $e) {
            
            
            return redirect()->route('dashboard')->with('error', 'No se pudo obtener la lista de cursos.');
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
    
            foreach ($courses as $course) {
                $detallesCurso = $assignedcourseM->obtenerCursosConDetallesPorEmpleado($course->id_usuarios);
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

// esta funcion es para guardar al alumno uno a uno 
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





    // este se encarga de las cargas masivas 
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



public function courses(Request $request, $idUsuario)
{
    try {
        $assignedcourseM = new AssignedcourseM();
        $cursos = $assignedcourseM->obtenerCursosConDetallesPorEmpleado($idUsuario);

        $coursesMatch = $cursos->isNotEmpty(); // 🔹 TRUE si hay cursos, FALSE si no

        return view('courses.assignedcourse.courses', [
            'idUsuario' => $idUsuario,
            'cursos' => $cursos,
            'coursesMatch' => $coursesMatch
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error en el servidor.',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function searchCoursesByUser(Request $request)
{
    try {
        $idUsuario = $request->input('idUsuario');
        $iterator = $request->input('iterator', 1);
        $searchValue = $request->input('searchValue', '');

        $request->validate([
            'idUsuario' => 'required|integer',
            'iterator' => 'required|integer|min:1',
        ]);

        $assignedcourseM = new AssignedcourseM();
        $cursos = $assignedcourseM->obtenerCursosConDetallesPorEmpleado($idUsuario, $iterator, $searchValue);

        return response()->json([
            'status' => true,
            'data' => $cursos->items(),
            'pagination' => [
                'current_page' => $cursos->currentPage(),
                'last_page' => $cursos->lastPage(),
                'per_page' => $cursos->perPage(),
                'total' => $cursos->total(),
            ]
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error en el servidor.',
            'error' => $e->getMessage()
        ], 500);
    }
}

  

// funciones de asignacion de cursos 

public function actualizarCursoSeleccionado($idEmpleadoCurso, $idCurso)
{
    $registro = DB::table('capacitacion.tbl_empleado_cursos')
        ->where('id_empleado_cursos', $idEmpleadoCurso)
        ->first();

    if (!$registro) return false;

    $idUsuario = $registro->id_usuarios;

    $yaInscrito = DB::table('capacitacion.tbl_empleado_cursos')
        ->where('id_usuarios', $idUsuario)
        ->where('id_cursos', $idCurso)
        ->exists();

    if ($yaInscrito) return 'duplicado';

    return DB::table('capacitacion.tbl_empleado_cursos')
        ->where('id_empleado_cursos', $idEmpleadoCurso)
        ->update([
            'id_cursos' => $idCurso,
            'id_usuario_sistema' => Auth::id(),
            'fecha_usuario' => now()
        ]);
}

// ✅ NUEVO: obtener el usuario desde un registro de curso
public function getUsuarioByIdEmpleadoCurso($idEmpleadoCurso)
{
    return DB::table('capacitacion.tbl_empleado_cursos')
        ->where('id_empleado_cursos', $idEmpleadoCurso)
        ->value('id_usuarios');
}

public function assigned($idUsuario)
{
    try {
        // 🔍 Buscar si ya hay un registro vacío para este usuario
        $registro = DB::table('capacitacion.tbl_empleado_cursos')
            ->where('id_usuarios', $idUsuario)
            ->whereNull('id_cursos')
            ->first();

        if (!$registro) {
            // 🆕 Si no hay, se crea uno nuevo
            $idEmpleadoCursos = DB::table('capacitacion.tbl_empleado_cursos')->insertGetId([
                'id_usuarios' => $idUsuario, // 👈 Alumno correcto
                'id_cursos' => null,
                'id_calificacion' => null,
                'estatus' => true,
                'id_usuario_sistema' => Auth::id(), // 👤 Usuario que hace el registro
                'fecha_usuario' => now()
            ], 'id_empleado_cursos'); // ✅ columna correcta (no 'id')

        } else {
            // 🧠 Ya existe, reutilizar ese
            $idEmpleadoCursos = $registro->id_empleado_cursos;
        }

        return view('courses.assignedcourse.assigned', compact('idEmpleadoCursos'));
    } catch (\Exception $e) {
        return redirect()->route('assignedcourse.list')->with('error', 'Ocurrió un error: ' . $e->getMessage());
    }
}


public function getCursosActivosAjax(Request $request)
{
    $model = new AssignedcourseM();
    $iterator = $request->input('iterator', 1);
    $search = $request->input('search', '');

    $cursos = $model->getCursosActivos($iterator, $search);

    return response()->json([
        'data' => $cursos->items(),
        'pagination' => [
            'current_page' => $cursos->currentPage(),
            'last_page' => $cursos->lastPage(),
            'total' => $cursos->total()
        ]
    ]);
}


public function enroll(Request $request)
{
    $request->validate([
        'id_empleado_cursos' => 'required|integer',
        'id_curso' => 'required|integer'
    ]);

    $idEmpleadoCurso = $request->input('id_empleado_cursos');
    $idCurso = $request->input('id_curso');

    // 🔍 Obtener el registro libre actual
    $registro = DB::table('capacitacion.tbl_empleado_cursos')
        ->where('id_empleado_cursos', $idEmpleadoCurso)
        ->whereNull('id_cursos')
        ->first();

    if (!$registro) {
        return redirect()->route('assignedcourse.list')->with('error', 'No se encontró el registro de inscripción disponible.');
    }

    $idUsuario = $registro->id_usuarios;

    // 🔒 Verificar que no esté inscrito ya en ese curso
    $yaInscrito = DB::table('capacitacion.tbl_empleado_cursos')
        ->where('id_usuarios', $idUsuario)
        ->where('id_cursos', $idCurso)
        ->exists();

    if ($yaInscrito) {
        return redirect()->route('assignedcourse.list')->with('error', 'Ya estás inscrito en este curso.');
    }

    // ✅ Actualizar el registro vacío con el nuevo curso
    DB::table('capacitacion.tbl_empleado_cursos')
        ->where('id_empleado_cursos', $idEmpleadoCurso)
        ->update([
            'id_cursos' => $idCurso,
            'id_usuario_sistema' => Auth::id(),
            'fecha_usuario' => now()
        ]);

    // 🆕 Crear un nuevo registro vacío SOLO después de inscribirse
    DB::table('capacitacion.tbl_empleado_cursos')->insert([
        'id_usuarios' => $idUsuario,
        'id_cursos' => null,
        'id_calificacion' => null,
        'estatus' => true,
        'id_usuario_sistema' => Auth::id(),
        'fecha_usuario' => now()
    ]);

    return redirect()->route('assignedcourse.list')->with('success', 'Curso asignado exitosamente.');
}



}