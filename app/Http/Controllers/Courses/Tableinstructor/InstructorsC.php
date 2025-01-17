<?php

namespace App\Http\Controllers\Courses\Tableinstructor;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\Instructores\Instructores\InstructorM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
        $instructorM = new InstructorM();
        $messagesC = new MessagesC();
        $now = Carbon::now();

        $request->validate([
            'estatus' => 'required|boolean',
        ]);

        $instructorM::create([
            'estatus' => $request->estatus,
            'id_usuario_sistema' => Auth::id(),
            'fecha_usuario' => $now,
        ]);

        return $messagesC->messageSuccessRedirect('tableinstructor.list', 'Instructor guardado exitosamente.');
    }

    public function create()
    {
        $item = new InstructorM();
        $item->estatus = '';
        return view('courses.tableinstructor.form', compact('item'));
    }

    public function searchTable(Request $request)
    {
        try {

            $iterator = $request->input('iterator'); //OFSET valor de paginador
            $searchValue = $request->input('searchValue');
            

            $instructorM = new InstructorM();
            $value = $instructorM ->list($iterator, $searchValue);

            return response()->json([ // Lógica para procesar la solicitud+
                'value' => $value,
                'status' => true,
            ]);

        } catch (\Exception $e) { // Manejo de errores  
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


    //BUSQUEDA DE CURP
    public function dataCurp(Request $request)
    {
        try {
            $request->validate([
                'curp' => 'required|string|size:18',
            ]);
    
            $instructorM = new InstructorM();
    
            $centralCurp = $instructorM->centralCurp($request->curp);
            $empleadoHRAES = $instructorM->buscarEmpleadoHRAES($request->curp);
            $empleadoTransferidos = $instructorM->buscarEmpleadoTransferidos($request->curp);
    
            $resultados = array_filter([$centralCurp, $empleadoHRAES, $empleadoTransferidos]);
    
            if (empty($resultados)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No se encontraron resultados para la CURP proporcionada.',
                    'value' => null,
                ], 200);
            }
    
            \Log::info('Resultados encontrados: ', $resultados); // Registro para verificar la respuesta
    
            return response()->json([
                'status' => true,
                'value' => $resultados,
                'message' => 'Datos encontrados correctamente',
            ], 200);
    
        } catch (\Exception $e) {
            \Log::error('Error en dataCurp: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error en el servidor: ' . $e->getMessage(),
            ], 500);
        }
    }
    
}
   

