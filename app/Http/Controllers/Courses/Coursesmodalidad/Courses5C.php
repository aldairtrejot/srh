<?php

namespace App\Http\Controllers\Courses\Coursesmodalidad;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\CoursesmodalidadM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class Courses5C extends Controller
{
    public function __invoke()
    {
        // Obtener todos los cursos de coordinación
        $coursesmodalidad = CoursesmodalidadM::all();

        // Pasar los cursos a la vista
        return view('courses/coursesmodalidad/list', compact('coursesmodalidad'));
    }

    public function save(Request $request)
    {
        $coursesmodalidadM = new CoursesmodalidadM();
        $messagesC = new MessagesC();
        $now = Carbon::now(); // Usando Carbon para la fecha actual
    
        if (!$request->id_cat_modalidad) {
            // Crear nuevo curso
            $nuevoCurso = $coursesmodalidadM::create([
                'descripcion' => $request->descripcion,
                'estatus' => $request->estatus ?? false,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ]);
        } else {
            // Modificar curso existente
            $data = [
                'descripcion' => $request->descripcion,
                'estatus' => $request->estatus ?? false,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];
    
            $coursesmodalidadM::where('id_cat_modalidad', $request->id_cat_modalidad)->update($data);
        }
    
        // Redirigir con mensaje de éxito
        return $messagesC->messageSuccessRedirect('coursesmodalidad.list', 'Curso guardado exitosamente.');
    }

    public function create()
    {
        $item = new CoursesmodalidadM();

        return view('courses.coursesmodalidad.form', compact('item'));
    }
    public function searchTable(Request $request)
    {
        $searchValue = strtoupper(trim($request->get('searchValue', '')));
        $iterator = intval($request->get('iterator', 0));
    
        $courses = CoursesmodalidadM::select([
                                'id_cat_modalidad AS id',
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
            $course = CoursesmodalidadM::findOrFail($id);
                $course->delete();
                return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']); 
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al eliminar el curso'], 500);
                
            }
            
    }
    public function edit(string $id)
    {
        $coursesmodalidadM = new CoursesmodalidadM();
        $item = $coursesmodalidadM->edit($id);

        return view('courses.coursesmodalidad.form', compact('item'));
       
    }

}