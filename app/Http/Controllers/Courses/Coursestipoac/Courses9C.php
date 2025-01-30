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
    
        if (!$request->id_cat_tipo_accion) {
            // Crear nuevo curso
            $nuevoCurso = $coursestipoacM::create([
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
    
            $coursestipoacM::where('id_cat_tipo_accion', $request->id_cat_tipo_accion)->update($data);
        }
    
        // Redirigir con mensaje de éxito
        return $messagesC->messageSuccessRedirect('coursestipoac.list', 'Curso guardado exitosamente.');
    }
    public function create()
    {
        $item = new CoursestipoacM();

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
    public function edit(string $id)
    {
        $coursestipoacM = new CoursestipoacM();
        $item = $coursestipoacM->edit($id);

        return view('courses.coursestipoac.form', compact('item'));
       
    }
}