<?php

namespace App\Http\Controllers\Courses\Coursesauditoria;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\CoursesauditoriaM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class Courses11C extends Controller
{
    public function __invoke()
{
    // Obtener todos los cursos
    $coursescategoria = CoursesauditoriaM::all();

    // Pasar los cursos a la vista
    return view('courses/coursesauditoria/list', compact('coursescategoria'));
}

    public function save(Request $request)
    {
        $coursescategoriaM = new CoursesauditoriaM();
        $messagesC = new MessagesC();
        $now = Carbon::now(); // Usando Carbon para la fecha actual
        // Validar los datos del formulario
        $request->validate([
            'descripcion' => 'required|string|max:255',
        ]);

        // Crear usuario
        $coursescategoriaM::create([
            'descripcion' => $request->descripcion,
            'estatus' => $request->estatus ?? false, // Manejar estatus como false si es null 
            'id_usuario_sistema' => Auth::user()->id,
            'fecha_usuario' => $now, 
        ]);

        // Redirigir a la lista de cursos con un mensaje de éxito
        //return redirect()->route('coursescategoria.list')->with('success', 'Curso guardado exitosamente.');
        return $messagesC->messageSuccessRedirect('coursesauditoria.list', 'Curso guardado exitosamente.');
    }

    public function create()
    {
        $item = new CoursesauditoriaM();
        $item->id_categoria = '';  // Set an empty value or default if needed
        $item->descripcion = '';    // Set an empty value or default if needed
        $item->estatus = '';     

        return view('courses.coursesauditoria.form', compact('item'));
    }
    public function searchTable(Request $request)
    {
        $searchValue = $request->get('searchValue');  // Término de búsqueda
        $iterator = $request->get('iterator', 0);  // Si no se pasa iterador, por defecto será 0 (primera página)

        // Filtrar los cursos que coincidan con la búsqueda
        $courses = CoursesauditoriaM::where('descripcion', 'like', '%' . $searchValue . '%')
            ->offset($iterator)
            ->limit(5)  // Límite de resultados por página
            ->get();

        return response()->json([
            'value' => $courses,
            'status' => true,
        ]);
    }
    public function destroy($id)
    {   
           try {
            $course = CoursesauditoriaM::findOrFail($id);
                $course->delete();
                return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']); 
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al eliminar el curso'], 500);
                
            }
            
    }
    public function edit(Request $request, $id)
    {
        $course = CoursesauditoriaM::find($id);
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
            //return redirect()->route('coursescategoria.list')->with('success', 'Curso actualizado exitosamente.');
            return $messagesC->messageSuccessRedirect('coursesauditoria.list', 'Curso actualizado exitosamente.');
        }

        return view('courses.coursesauditoria.edit', compact('course'));
    }
}
