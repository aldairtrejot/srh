<?php

namespace App\Http\Controllers\Courses\Coursesorganizacion;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\CoursesorganizacionM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;
class Courses7C extends Controller
{
    public function __invoke()
    {
        // Obtener todos los cursos de coordinación
        $coursesorganizacion = CoursesorganizacionM::all();
        // Pasar los cursos a la vista
        return view('courses/coursesorganizacion/list', compact('coursesorganizacion'));
    }

    public function save(Request $request)
    {
        $coursesorganizacionM = new CoursesorganizacionM();
        $messagesC = new MessagesC();
        $now = Carbon::now(); // Usando Carbon para la fecha actual
    
        if (!$request->id_cat_organizacion) {
            // Crear nuevo curso
            $nuevoCurso = $coursesorganizacionM::create([
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
    
            $coursesorganizacionM::where('id_cat_organizacion', $request->id_cat_organizacion)->update($data);
        }
    
        // Redirigir con mensaje de éxito
        return $messagesC->messageSuccessRedirect('coursesorganizacion.list', 'Curso guardado exitosamente.');
    }
    public function create()
    {
        $item = new CoursesorganizacionM();   

        return view('courses.coursesorganizacion.form', compact('item'));
    }
    public function searchTable(Request $request)
    {
        $searchValue = $request->get('searchValue');  // Término de búsqueda
        $iterator = $request->get('iterator', 0);  // Si no se pasa iterador, por defecto será 0 (primera página)
    
        // Filtrar los cursos que coincidan con la búsqueda
        $courses = CoursesorganizacionM::where('descripcion', 'like', '%' . $searchValue . '%')
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
            $course = CoursesorganizacionM::findOrFail($id);
                $course->delete();
                return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']); 
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al eliminar el curso'], 500);
                
            }
            
    }
    public function edit(string $id)
    {
        $coursesorganizacionM = new CoursesorganizacionM();
        $item = $coursesorganizacionM->edit($id);

        return view('courses.coursesorganizacion.form', compact('item'));
       
    }
}