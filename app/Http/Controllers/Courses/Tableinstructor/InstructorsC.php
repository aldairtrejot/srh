<?php

namespace App\Http\Controllers\Courses\Tableinstructor;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\InstructorM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class InstructorsC extends Controller
{
    public function list()
    {
        return view('courses/tableinstructor/list');
    }

    // La función crea la tabla dependiendo de los roles que se han ingresado
    public function table(Request $request)
    {
        try {
            $instructorM = new InstructorM();
            // Obtener valores de la solicitud
            $iterator = $request->input('iterator'); // OFSET valor de paginador
            $searchValue = $request->input('searchValue'); // Valor de búsqueda
            $roleUserArray = collect(session('SESSION_ROLE_USER'))->toArray(); // Array con roles de usuario
            $ADM_TOTAL = config('custom_config.ADM_TOTAL'); // Acceso completo
            $COR_TOTAL = config('custom_config.COR_TOTAL'); // Acceso completo a correspondencia
            $COR_USUARIO = config('custom_config.COR_USUARIO'); // Acceso por área

            // Verificar si el usuario tiene acceso completo
            if (in_array($ADM_TOTAL, $roleUserArray) || in_array($COR_TOTAL, $roleUserArray)) {
                // Si tiene acceso completo, no hay necesidad de filtrar por área o enlace
                // Procesar la tabla con acceso completo si es necesario
                $value = $instructorM->list($iterator, $searchValue, null);
            } else {
                // Llamamos al método list() con los parámetros necesarios
                $value = $instructorM->list($iterator, $searchValue, Auth::id());
            }

            // Responder con los resultados
            return response()->json([
                'value' => $value,
                'status' => true,
            ]);

        } catch (\Exception $e) {
            // Manejo de errores en caso de excepciones
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function create()
    {
        $item = new InstructorM();
        $item->id_empleados = '';  // Set an empty value or default if needed
        $item->uuid_constancia = '';    // Set an empty value or default if needed
        $item->uuid_cv = '';    
        $item->estatus_apto = '';   
        $item->id_usuario_sistema = '';   
        $item->fecha_usuario = '';   
        return view('courses.tableinstructor.form', compact('item'));
    }

    public function edit(Request $request, string $id)
    {
        $instructorM = new InstructorM();
        $messagesC = new MessagesC();

        if ($request->isMethod('post')) {
            // Validar los datos del formulario
            $request->validate([
                'descripcion' => 'required|string|max:255',
            ]);

            // Actualizar los datos del curso
            $instructorM->estatus = $request->input('estatus') ? true : false;
            $instructorM->save();

            // Redirigir a la lista de cursos con un mensaje de éxito
            return $messagesC->messageSuccessRedirect('tableinstructor.list', 'Curso actualizado exitosamente.');
        }

        return view('courses.tableinstructor.edit', compact('instructorM'));
    }

    public function save(Request $request)
    {
        $instructorM = new InstructorM();
        $messagesC = new MessagesC();
      
        $roleUserArray = collect(session('SESSION_ROLE_USER'))->toArray(); // Array con roles de usuario
        $ADM_TOTAL = config('custom_config.ADM_TOTAL'); // Acceso completo
        $COR_TOTAL = config('custom_config.COR_TOTAL'); // Acceso completo a correspondencia
        // Autorización solo administración

        $now = Carbon::now(); // Hora y fecha actual

        if (!isset($request->id_instructor)) { // Creación de nuevo elemento

            $data = [
                'id_empleados' => $request->id_empleados,
                'uuid_constancia' => $request->uuid_constancia,
                'uuid_cv' => $request->uuid_cv,
                'estatus_apto' => $request->estatus_apto,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];
        
            $instructorM::create($data);
        
            return $messagesC->messageSuccessRedirect('tableinstructor.list', 'Elemento agregado con éxito.');
        
        } else { // Modificar elemento
        
            $data = [
                'id_empleados' => $request->id_empleados,
                'uuid_constancia' => $request->uuid_constancia,
                'uuid_cv' => $request->uuid_cv,
                'estatus_apto' => $request->estatus_apto,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];
        
            // Actualización en db
            $instructorM::where('id_instructor', $request->id_instructor)
                ->update($data);
        
            // Log app
            $data['id_instructor'] = $request->id_instructor;
        
            return $messagesC->messageSuccessRedirect('tableinstructor.list', 'Elemento modificado con éxito.');
        }
    }
}