<?php

namespace App\Http\Controllers\Courses\Coursesestatuto;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\CoursesestatutoM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class Courses4C extends Controller
{
    public function __invoke()
    {
        // Obtener todos los cursos de coordinación
        $coursesestatuto = CoursesestatutoM::all();

        // Pasar los cursos a la vista
        return view('courses/coursesestatuto/list', compact('coursesestatuto'));
    }

    public function save(Request $request)
    {
        $coursesestatutoM = new CoursesestatutoM();
        $messagesC = new MessagesC();
        $now = Carbon::now(); // Usando Carbon para la fecha actual
    
        if (!$request->id_cat_estatuto_organico) {
            // Crear nuevo curso
            $nuevoCurso = $coursesestatutoM::create([
                'descripcion' => $request->descripcion,
                'estatus' => $request->estatus ?? false,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
                'nombre' => $request->nombre,
            ]);
        } else {
            // Modificar curso existente
            $data = [
                'descripcion' => $request->descripcion,
                'estatus' => $request->estatus ?? false,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
                'nombre' => $request->nombre,
            ];
    
            $coursesestatutoM::where('id_cat_estatuto_organico', $request->id_cat_estatuto_organico)->update($data);
        }
    
        // Redirigir con mensaje de éxito
        return $messagesC->messageSuccessRedirect('coursesestatuto.list', 'Curso guardado exitosamente.');
    }
    public function create()
    {
        $item = new CoursesestatutoM();

        return view('courses.coursesestatuto.form', compact('item'));
    }
    public function searchTable(Request $request)
    {
        $searchValue = $request->get('searchValue');  // Término de búsqueda
        $iterator = $request->get('iterator', 0);  // Si no se pasa iterador, por defecto será 0 (primera página)
    
        // Filtrar los cursos que coincidan con la búsqueda
        $courses = CoursesestatutoM::where('descripcion', 'like', '%' . $searchValue . '%')
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
            $course = CoursesestatutoM::findOrFail($id);
                $course->delete();
                return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']); 
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al eliminar el curso'], 500);
                
            }
            
    }
    public function edit(string $id)
    {
        $coursesestatutoM = new CoursesestatutoM();
        $item = $coursesestatutoM->edit($id);

        return view('courses.coursesestatuto.form', compact('item'));
       
    }
    
}