<?php

namespace App\Http\Controllers\Courses\Tableinstructor;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\Instructores\Instructores\InstructorM;
use App\Models\Courses\Courses\Instructores\Instructores\UpdateInstructorM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\MessagesC;
use App\Models\Courses\Tableinstructor\CollectionStatusM;


class InstructorsC extends Controller
{
    public function __invoke()
    {
        $tableInstructors = InstructorM::all();
        return view('courses.tableinstructor.list', compact('tableInstructors'));
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
                return $this->update($request);
            }

            $instructorM = new InstructorM();
            $idInstructor = $instructorM->obtenerOcrearInstructor($request->curp, $request->estatus);

            if (!$idInstructor) {
                return $messagesC->messageErrorRedirect('tableinstructor.list', 'Error al registrar instructor.');
            }

            return $messagesC->messageSuccessRedirect('tableinstructor.list', 'Instructor registrado correctamente.');
        } catch (\Exception $e) {
            return $messagesC->messageErrorRedirect('tableinstructor.list', 'Error: ' . $e->getMessage());
        }
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

public function create()
{
    $item = new InstructorM();

    // Inicializar valores por defecto
    $item->estatus_instructor = 1; // Activo por defecto

    return view('courses.tableinstructor.form', compact('item'));
}

public function edit($id)
{
    $updateInstructorM = new UpdateInstructorM();
    $item = $updateInstructorM->editInstructor($id);

    if (!$item) {
        return redirect()->route('tableinstructor.list')->with('error', 'Instructor no encontrado.');
    }

    Log::info("✅ Datos enviados a la vista: ", (array) $item); // REGISTRA LOS DATOS EN EL LOG

    return view('courses.tableinstructor.form', compact('item'));
}

public function update(Request $request, $id)
{
    try {
        Log::info('🔄 Datos recibidos en update():', $request->all());

        // Validar CURP y Estatus
        $request->validate([
            'curp' => 'required|string|size:18',
            'estatus' => 'required|in:0,1',
        ]);

        $updateInstructorM = new UpdateInstructorM();

        Log::info("📌 Intentando actualizar CURP: {$request->curp} y Estatus: {$request->estatus}");

        // Actualizar la CURP
        $curpActualizado = $updateInstructorM->updateCurpInstructor($id, $request->curp);

        // Actualizar el estatus
        $estatusActualizado = DB::table('capacitacion.tbl_instructores')
            ->where('id_tbl_instructores', $id)
            ->update(['estatus' => $request->estatus]);

        if (!$curpActualizado && !$estatusActualizado) {
            Log::error("❌ No se pudo actualizar la CURP ni el estatus para el instructor ID: {$id}");
            return response()->json(['status' => false, 'message' => 'No se pudo actualizar la CURP ni el estatus.'], 500);
        }

        Log::info("✅ Instructor actualizado correctamente para el ID: {$id}");

        return response()->json(['status' => true, 'message' => 'Instructor actualizado correctamente.']);

    } catch (\Exception $e) {
        Log::error("🔥 Error en update(): " . $e->getMessage());
        return response()->json(['status' => false, 'message' => 'Error en el servidor.'], 500);
    }
}



public function updateCurp(Request $request, $id)
{
    try {
        Log::info("📝 Datos recibidos en updateCurp():", $request->all());

        // Validar CURP
        $request->validate([
            'curp' => 'required|string|size:18',
        ]);

        // Llamar al modelo para actualizar la CURP
        $updateInstructorM = new UpdateInstructorM();
        $resultado = $updateInstructorM->updateCurpInstructor($id, $request->curp);

        if (!$resultado) {
            Log::error("❌ No se pudo actualizar la CURP para el instructor ID: {$id}");
            return response()->json([
                'status' => false,
                'message' => 'No se pudo actualizar la CURP.',
            ], 500);
        }

        Log::info("✅ CURP actualizada correctamente para el instructor ID: {$id}");
        return response()->json([
            'status' => true,
            'message' => 'CURP actualizada correctamente.',
        ]);
    } catch (\Exception $e) {
        Log::error("🔥 Error en updateCurp(): " . $e->getMessage());
        return response()->json([
            'status' => false,
            'message' => 'Error en el servidor: ' . $e->getMessage(),
        ], 500);
    }
}


}