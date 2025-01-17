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
        // Validar los datos del formulario
        $request->validate([
            'descripcion' => 'required|string|max:255',
           
        ]);

        // Crear usuario
        $coursesM::create([
            'descripcion' => $request->descripcion,
            'estatus' => $request->estatus ?? false,
            'id_usuario_sistema' => Auth::user()->id,
            'fecha_usuario' => $now, // Manejar estatus como false si es null
        ]);

        // Redirigir a la lista de cursos con un mensaje de éxito
        //return redirect()->route('courses.list')->with('success', 'Curso guardado exitosamente.');
        return $messagesC->messageSuccessRedirect('courses.list', 'Curso guardado exitosamente.');
    }

    public function create()
    {
        $item = new CoursesM();
        $item->id_cat_beneficio = '';  // Set an empty value or default if needed
        $item->descripcion = '';    // Set an empty value or default if needed
        $item->estatus = '';     

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
    public function edit(Request $request, $id)
    {
        $course = CoursesM::find($id);
        $messagesC = new MessagesC();

        if ($request->isMethod('post')) {
            // Validar los datos del formulario
            $request->validate([
                'descripcion' => 'required|string|max:255',
            ]);

            // Actualizar los datos del curso
            $course->descripcion = $request->input('descripcion');
            $course->estatus = $request->input('estatus') ? true : false;
            $course->save();

            // Redirigir a la lista de cursos con un mensaje de éxito
            //return redirect()->route('courses.list')->with('success', 'Curso actualizado exitosamente.');
            return $messagesC->messageSuccessRedirect('courses.list', 'Curso actualizado exitosamente.');
        }

       return view('courses.courses.edit', compact('course'));
       
    }
}


