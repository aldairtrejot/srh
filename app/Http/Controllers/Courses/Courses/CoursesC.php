<?php

namespace App\Http\Controllers\Courses\Courses;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\CoursesM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class CoursesC extends Controller
{
    public function __invoke()
{
    // Obtener todos los cursos
    $courses = CoursesM::all();

    // Pasar los cursos a la vista
    return view('courses/courses/list', compact('courses'));
}

public function save(Request $request)
{
    $coursesM = new CoursesM();
    $messagesC = new MessagesC();
    $now = Carbon::now(); // Usando Carbon para la fecha actual

    if (!$request->id_cat_beneficio) {
        // Crear nuevo curso
        $nuevoCurso = $coursesM::create([
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

        $coursesM::where('id_cat_beneficio', $request->id_cat_beneficio)->update($data);
    }

    // Redirigir con mensaje de éxito
    return $messagesC->messageSuccessRedirect('courses.list', 'Curso guardado exitosamente.');
}


    public function create()
    {
        $item = new CoursesM();
        
        return view('courses.courses.form', compact('item'));
    }
    public function searchTable(Request $request)
    {
        $searchValue = $request->get('searchValue');  // Término de búsqueda
        $iterator = $request->get('iterator', 0);  // Si no se pasa iterador, por defecto será 0 (primera página)
    
        // Filtrar los cursos que coincidan con la búsqueda
        $courses = CoursesM::where('descripcion', 'like', '%' . $searchValue . '%')
                           ->offset($iterator)
                           ->limit(5)  // Límite de resultados por página
                           ->get();
    
        return response()->json([
            'value' => $courses
        ]);
    }
    
    // Otros métodos del controlador...

    public function destroy($id)
    {   
           try {
            $course = CoursesM::findOrFail($id);
                $course->delete();
                return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']); 
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al eliminar el curso'], 500);
                
            }
            
    }
    public function edit(string $id)
    {
        $coursesM = new CoursesM();
        $item = $coursesM->edit($id);

        return view('courses.courses.form', compact('item'));
       
    }
}


