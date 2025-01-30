<?php

namespace App\Http\Controllers\Courses\Coursestipocur;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\CoursestipocurM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class Courses10C extends Controller
{
    public function __invoke()
    {
        // Obtener todos los cursos de coordinación
        $coursestipocur = CoursestipocurM::all();
    
        // Pasar los cursos a la vista
        return view('courses/coursestipocur/list', compact('coursestipocur'));
    }

    public function save(Request $request)
    {
        $coursestipocurM = new CoursestipocurM();
        $messagesC = new MessagesC();
        $now = Carbon::now(); // Usando Carbon para la fecha actual
    
        if (!$request->id_cat_tipo_cursos) {
            // Crear nuevo curso
            $nuevoCurso = $coursestipocurM::create([
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
    
            $coursestipocurM::where('id_cat_tipo_cursos', $request->id_cat_tipo_cursos)->update($data);
        }
    
        // Redirigir con mensaje de éxito
        return $messagesC->messageSuccessRedirect('coursestipocur.list', 'Curso guardado exitosamente.');
    }

    public function create()
    {
        $item = new CoursestipocurM();  

        return view('courses.coursestipocur.form', compact('item'));
    }
    public function searchTable(Request $request)
    {
        $searchValue = $request->get('searchValue');  // Término de búsqueda
        $iterator = $request->get('iterator', 0);  // Si no se pasa iterador, por defecto será 0 (primera página)
    
        // Filtrar los cursos que coincidan con la búsqueda
        $courses = CoursestipocurM::where('descripcion', 'like', '%' . $searchValue . '%')
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
            $course = CoursestipocurM::findOrFail($id);
                $course->delete();
                return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']); 
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al eliminar el curso'], 500);
                
            }
            
    }
    public function edit(string $id)
    {
        $coursestipocurM = new CoursestipocurM();
        $item = $coursestipocurM->edit($id);

        return view('courses.coursestipocur.form', compact('item'));
       
    }

}