<?php

namespace App\Http\Controllers\Courses\Coursesprograma;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\CoursesprogramaM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class Courses8C extends Controller
{
    public function __invoke()
    {
        // Obtener todos los cursos de coordinación
        $coursesprograma = CoursesprogramaM::all();
    
        // Pasar los cursos a la vista
        return view('courses/coursesprograma/list', compact('coursesprograma'));
    }

    public function save(Request $request)
    {
        $coursesprogramaM = new CoursesprogramaM();
        $messagesC = new MessagesC();
        $now = Carbon::now(); // Usando Carbon para la fecha actual
        // Validar los datos del formulario
        $request->validate([
            'descripcion' => 'required|string|max:255',
        ]);

        // Crear usuario
        $coursesprogramaM::create([
            'descripcion' => $request->descripcion,
            'estatus' => $request->estatus ?? false, // Manejar estatus como false si es null
            'id_usuario_sistema' => Auth::user()->id,
            'fecha_usuario' => $now,
            'nombre' => $request->nombre,
        ]);

        // Redirigir a la lista de cursos con un mensaje de éxito
        //return redirect()->route('coursesprograma.list')->with('success', 'Curso guardado exitosamente.');
        return $messagesC->messageSuccessRedirect('coursesprograma.list', 'Curso guardado exitosamente.');
    }

    public function create()
    {
        $item = new CoursesprogramaM();
        $item->id_cat_programa_institucional = '';  // Valor por defecto
        $item->descripcion = '';    // Valor por defecto
        $item->estatus = '';   
        $item->nombre = '';      

        return view('courses.coursesprograma.form', compact('item'));
    }
    public function searchTable(Request $request)
    {
        $searchValue = $request->get('searchValue');  // Término de búsqueda
        $iterator = $request->get('iterator', 0);  // Si no se pasa iterador, por defecto será 0 (primera página)
    
        // Filtrar los cursos que coincidan con la búsqueda
        $courses = CoursesprogramaM::where('descripcion', 'like', '%' . $searchValue . '%')
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
            $course = CoursesprogramaM::findOrFail($id);
                $course->delete();
                return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']); 
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al eliminar el curso'], 500);
                
            }
            
    }
    public function edit(Request $request, $id)
    {
        $course = CoursesprogramaM ::find($id);
        $messagesC = new MessagesC();

        if ($request->isMethod('post')) {
            // Validar los datos del formulario
            $request->validate([
                'descripcion' => 'required|string|max:255',
            ]);

            // Actualizar los datos del curso
            $course->descripcion = $request->input('descripcion');
            $course->estatus = $request->input('estatus') ? true : false;
            $course->nombre = $request->input('nombre');
            $course->save();

            // Redirigir a la lista de cursos con un mensaje de éxito
            //return redirect()->route('coursesprograma.list')->with('success', 'Curso actualizado exitosamente.');
            return $messagesC->messageSuccessRedirect('coursesprograma.list', 'Curso actualizado exitosamente.');
        }

        return view('courses.coursesprograma.edit', compact('course'));
    }
}