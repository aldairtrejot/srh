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
        // Validar los datos del formulario
        $request->validate([
            'descripcion' => 'required|string|max:255',
        ]);

        // Crear usuario
        $coursesmodalidadM::create([
            'descripcion' => $request->descripcion,
            'estatus' => $request->estatus ?? false,// Manejar estatus como false si es null
            'id_usuario_sistema' => Auth::user()->id,
            'fecha_usuario' => $now,  
        ]);

        // Redirigir a la lista de cursos con un mensaje de éxito
        //return redirect()->route('coursesmodalidad.list')->with('success', 'Curso guardado exitosamente.');
        return $messagesC->messageSuccessRedirect('coursesmodalidad.list', 'Curso guardado exitosamente.');
    }

    public function create()
    {
        $item = new CoursesmodalidadM();
        $item->id_cat_modalidad = '';  // Valor por defecto
        $item->descripcion = '';    // Valor por defecto
        $item->estatus = '';     
       

        return view('courses.coursesmodalidad.form', compact('item'));
    }
    public function searchTable(Request $request)
    {
        $searchValue = $request->get('searchValue');  // Término de búsqueda
        $iterator = $request->get('iterator', 0);  // Si no se pasa iterador, por defecto será 0 (primera página)
    
        // Filtrar los cursos que coincidan con la búsqueda
        $courses = CoursesmodalidadM::where('descripcion', 'like', '%' . $searchValue . '%')
                           ->offset($iterator)
                           ->limit(5)  // Límite de resultados por página
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
    public function edit(Request $request, $id)
    {
        $course = CoursesmodalidadM ::find($id);
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
           // return redirect()->route('coursesmodalidad.list')->with('success', 'Curso actualizado exitosamente.');
           return $messagesC->messageSuccessRedirect('coursesmodalidad.list', 'Curso actualizado exitosamente.');
        }

        return view('courses.coursesmodalidad.edit', compact('course'));
    }

}