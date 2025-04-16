<?php

namespace App\Http\Controllers\Courses\Coursesnombreacc;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\CoursesnombreaccM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class Courses6C extends Controller
{
    public function __invoke()
    {
        // Obtener todos los cursos de coordinación
        $coursesnombreacc = CoursesnombreaccM::all();

        // Pasar los cursos a la vista
        return view('courses/coursesnombreacc/list', compact('coursesnombreacc'));
    }

    public function save(Request $request)
    {
        $coursesnombreaccM = new CoursesnombreaccM();
        $messagesC = new MessagesC();
        $now = Carbon::now(); // Usando Carbon para la fecha actual
    
        if (!$request->id_cat_nombre_accion) {
            // Crear nuevo curso
            $nuevoCurso = $coursesnombreaccM::create([
                'descripcion' => $request->descripcion,
                'estatus' => $request->estatus ?? false,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
                'nombre' => $request->nombre,
            ]);
        } else {
            // Modificar curso existente
            $data = [
                'descripcion' => $request->descripcion,
                'estatus' => $request->estatus ?? false,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
                'nombre' => $request->nombre,
            ];
    
            $coursesnombreaccM::where('id_cat_nombre_accion', $request->id_cat_nombre_accion)->update($data);
        }
    
        // Redirigir con mensaje de éxito
        return $messagesC->messageSuccessRedirect('coursesnombreacc.list', 'Curso guardado exitosamente.');
    }
    public function create()
    {
        $item = new CoursesnombreaccM();

        return view('courses.coursesnombreacc.form', compact('item'));
    }
    public function searchTable(Request $request)
    {
        $searchValue = strtoupper(trim($request->get('searchValue', '')));
        $iterator = intval($request->get('iterator', 0));
    
        $courses = CoursesnombreaccM::select([
                                'id_cat_nombre_accion AS id',
                                'descripcion',
                                'estatus'
                            ])
                            ->whereRaw("UPPER(TRIM(descripcion)) LIKE ?", ["%$searchValue%"])
                            ->offset($iterator)
                            ->limit(5)
                            ->get();
    
        return response()->json([
            'value' => $courses
        ]);
    }

    public function destroy($id)
    {   
           try {
            $course = CoursesnombreaccM::findOrFail($id);
                $course->delete();
                return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']); 
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al eliminar el curso'], 500);
                
            }
            
    }

    public function edit(string $id)
    {
        $coursesnombreaccM = new CoursesnombreaccM();
        $item = $coursesnombreaccM->edit($id);

        return view('courses.coursesnombreacc.form', compact('item'));
       
    }
}