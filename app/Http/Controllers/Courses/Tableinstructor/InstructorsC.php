<?php

namespace App\Http\Controllers\Courses\Tableinstructor;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\Instructores\Instructores\InstructorM;
use App\Models\Courses\Courses\Instructores\Instructores\UserM;
use App\Models\Courses\Courses\Instructores\Instructores\TblinstructoresM;
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
        $now = Carbon::now(); // Fecha actual
    
        if (!$request->id) {
            // Crear nuevo usuario
            $nuevoUsuario = UserM::create([
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => $request->email_verified_at,
                'password' => bcrypt($request->password), // Encriptar contraseña
                'remember_token' => $request->remember_token,
                'id_tbl_empleados_central' => $request->id_tbl_empleados_central,
                'id_tbl_empleados_hraes' => $request->id_tbl_empleados_hraes,
                'id_tbl_empleados_transferidos' => $request->id_tbl_empleados_transferidos,
                'id_tbl_empleados_aux' => $request->id_tbl_empleados_aux,
                'es_por_nomina' => $request->es_por_nomina,
                'estatus' => $request->estatus ?? false,
                'id_usuario' => Auth::id(), // Tomar el usuario autenticado
                'fecha_usuario' => $now,
                'id_cat_tipo_schema' => $request->id_cat_tipo_schema,
            ]);
    
            // Obtener ID del usuario creado
            $idUsuario = $nuevoUsuario->id; 
    
            // Crear relación en la tabla de instructores
            TblinstructoresM::create([
                'id_usuario_sistema' => Auth::id(),
                'id_usuario_empleado' => $idUsuario,
                'id_tbl_instructores' => $request->id_tbl_instructores,
                'fecha_usuario' => $now
            ]);
        }
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
        \Log::info('Recibiendo CURP: ' . $request->curp);
        $request->validate([
            'curp' => 'required|string|size:18',
        ]);

        $instructorM = new InstructorM();

        \Log::info('Buscando datos en centralCurp...');
        $centralCurp = $instructorM->centralCurp($request->curp);

        \Log::info('Buscando datos en buscarEmpleadoHRAES...');
        $empleadoHRAES = $instructorM->buscarEmpleadoHRAES($request->curp);

        \Log::info('Buscando datos en buscarEmpleadoTransferidos...');
        $empleadoTransferidos = $instructorM->buscarEmpleadoTransferidos($request->curp);

        $resultados = array_filter([$centralCurp, $empleadoHRAES, $empleadoTransferidos]);

        if (empty($resultados)) {
            \Log::info('No se encontraron resultados para el CURP.');
            return response()->json([
                'status' => false,
                'message' => 'No se encontraron resultados para la CURP proporcionada.',
                'value' => null,
            ], 200);
        }

        \Log::info('Datos encontrados: ' . json_encode($resultados));
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
