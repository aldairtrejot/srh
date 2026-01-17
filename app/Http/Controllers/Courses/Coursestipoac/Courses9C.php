<?php

namespace App\Http\Controllers\Courses\Coursestipoac;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\CoursestipoacM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class Courses9C extends Controller
{
    public function __invoke()
    {
        // Obtener todos los cursos de coordinación
        $coursestipoac = CoursestipoacM::all();
    
        // Pasar los cursos a la vista
        return view('courses/coursestipoac/list', compact('coursestipoac'));
    }

    public function save(Request $request)
    {
        $coursestipoacM = new CoursestipoacM();
        $messagesC = new MessagesC();
        $now = Carbon::now(); // Usando Carbon para la fecha actual
        // Validar los datos del formulario
        $request->validate([
            'descripcion' => 'required|string|max:255',
        ]);

        // Crear usuario
        $coursestipoacM::create([
            'descripcion' => $request->descripcion,
            'estatus' => $request->estatus ?? false, // Manejar estatus como false si es null
            'id_usuario_sistema' => Auth::user()->id,
            'fecha_usuario' => $now,
        ]);

        // Redirigir a la lista de cursos con un mensaje de éxito
        //return redirect()->route('coursestipoac.list')->with('success', 'Curso guardado exitosamente.');
        return $messagesC->messageSuccessRedirect('coursestipoac.list', 'Curso guardado exitosamente.');
    }

    public function create()
    {
        $item = new CoursestipoacM();
        $item->id_cat_tipo_accion = '';  // Valor por defecto
        $item->descripcion = '';    // Valor por defecto
        $item->estatus = '';     

        return view('courses.coursestipoac.form', compact('item'));
    }
    public function searchTable(Request $request)
    {
        $searchValue = $request->get('searchValue');  // Término de búsqueda
        $iterator = $request->get('iterator', 0);  // Si no se pasa iterador, por defecto será 0 (primera página)
    
        // Filtrar los cursos que coincidan con la búsqueda
        $courses = CoursestipoacM::where('descripcion', 'like', '%' . $searchValue . '%')
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
            $course = CoursestipoacM::findOrFail($id);
                $course->delete();
                return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']); 
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al eliminar el curso'], 500);
                
            }
            
    }
    public function edit(Request $request, $id)
    {
        $course = CoursestipoacM ::find($id);
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
            //return redirect()->route('coursestipoac.list')->with('success', 'Curso actualizado exitosamente.');
            return $messagesC->messageSuccessRedirect('coursestipoac.list', 'Curso actualizado exitosamente.');
        }

        return view('courses.coursestipoac.edit', compact('course'));
    }
}