<?php

namespace App\Http\Controllers\Courses\Tableinstructor;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\Instructores\Instructores\InstructorM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;  
use App\Http\Controllers\Admin\MessagesC;

class InstructorsC extends Controller
{
    public function __invoke()
    {
        $tableInstructors = InstructorM::all();
        return view('courses.tableinstructor.list', compact('tableInstructors'));
    }

    public function save(Request $request)
{
    \Log::info('🚀 Entrando en save() con CURP: ' . $request->curp);
    \Log::info('📩 Datos recibidos en request:', $request->all());

    try {
        $now = Carbon::now();
        $messagesC = new MessagesC();
        $request->validate([
            'curp' => 'required|string|size:18',
        ]);

        // Obtener usuario desde el modelo
        $instructorM = new InstructorM();
        $idUsuario = $instructorM->obtenerOcrearUsuarioPorCurp($request->curp);

        if (!$idUsuario) {
            \Log::error('❌ No se pudo obtener un ID de usuario.');
            return $messagesC->messageErrorRedirect('tableinstructor.list', 'No se pudo obtener un usuario válido.');
        }

        \Log::info('✅ Usuario registrado en administration.users con ID: ' . $idUsuario);

        // Guardar en `capacitacion.tbl_instructores`
        $idInstructor = $instructorM->obtenerOcrearInstructor($request->curp, $request->estatus);

        if (!$idInstructor) {
            \Log::error('❌ No se pudo registrar el instructor en capacitacion.tbl_instructores.');
            return $messagesC->messageErrorRedirect('tableinstructor.list', 'No se pudo registrar el instructor.');
        }

        return $messagesC->messageSuccessRedirect('tableinstructor.list', 'Instructor registrado correctamente.');

    } catch (\Exception $e) {
        \Log::error('🔥 Error en save(): ' . $e->getMessage());
        return $messagesC->messageErrorRedirect('tableinstructor.list', 'Error en el servidor: ' . $e->getMessage());
    }
}


    public function create()
    {
        $item = new InstructorM();
        return view('courses.tableinstructor.form', compact('item'));
    }


    public function searchTable(Request $request)
    {
        try {
            $iterator = $request->input('iterator'); // OFSET valor de paginador
            $searchValue = $request->input('searchValue');

            $instructorM = new InstructorM();
            $value = $instructorM->list($iterator, $searchValue);

            return response()->json([ 
                'value' => $value,
                'status' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $instructor = InstructorM::findOrFail($id);
            $instructor->delete();

            return response()->json(['success' => true, 'message' => 'Instructor eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el instructor.'], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        $instructor = InstructorM::find($id);
        $messagesC = new MessagesC();

        if (!$instructor) {
            abort(404, 'Instructor no encontrado.');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'estatus' => 'required|boolean',
            ]);

            $instructor->estatus = $request->input('estatus') ? true : false;
            $instructor->save();

            return $messagesC->messageSuccessRedirect('tableinstructor.list', 'Instructor actualizado exitosamente.');
        }

        return view('courses.tableinstructor.edit', compact('instructor'));
    }

    // BUSQUEDA DE CURP
    // Método dataCurp en InstructorsC.php
    public function dataCurp(Request $request)
{
    try {
        $request->validate([
            'curp' => 'required|string|size:18',
        ]);

        \Log::info("CURP recibida: " . $request->curp); // 📌 Depuración: Verificar que la CURP llegue al servidor
        $messagesC = new MessagesC();
        $instructorM = new InstructorM();

        // Obtener los datos
        $centralCurp = $instructorM->centralCurp($request->curp);
        $empleadoHRAES = $instructorM->buscarEmpleadoHRAES($request->curp);
        $empleadoTransferidos = $instructorM->buscarEmpleadoTransferidos($request->curp);

        // Validar y devolver un objeto, no un array vacío
        $resultado = $centralCurp ?? $empleadoHRAES ?? $empleadoTransferidos;

        if ($resultado) {
            return response()->json([
                'status' => true,
                'value' => is_array($resultado) ? (object) $resultado[0] : (object) $resultado, // 📌 Garantiza que siempre sea un objeto
                $messagesC->messageSuccessRedirect('tableinstructor.create', 'CURP.'),
            ], 200);
        }

        // Si no se encuentran datos
        return response()->json([
            'status' => false,
            'value' => null, // 📌 Importante para evitar `undefined`
            $messagesC->messageSuccessRedirect('tableinstructor.create', 'CURP.'),
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error en el servidor: ' . $e->getMessage(),
        ], 500);
    }
}
}