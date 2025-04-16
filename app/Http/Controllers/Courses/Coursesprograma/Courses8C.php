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
    
        if (!$request->id_cat_programa_institucional) {
            // Crear nuevo curso
            $nuevoCurso = $coursesprogramaM::create([
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
    
            $coursesprogramaM::where('id_cat_programa_institucional', $request->id_cat_programa_institucional)->update($data);
        }
    
        // Redirigir con mensaje de éxito
        return $messagesC->messageSuccessRedirect('coursesprograma.list', 'Curso guardado exitosamente.');
    }

    public function create()
    {
        $item = new CoursesprogramaM(); 

        return view('courses.coursesprograma.form', compact('item'));
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
    public function edit(string $id)
    {
        $coursesprogramaM = new CoursesprogramaM();
        $item = $coursesprogramaM->edit($id);

        return view('courses.coursesprograma.form', compact('item'));     
    }

    public function searchTable(Request $request)
    {
        $searchValue = strtoupper(trim($request->get('searchValue', '')));
        $iterator = intval($request->get('iterator', 0));
    
        $courses = CoursesprogramaM::select([
                                'id_cat_programa_institucional AS id',
                                'descripcion',
                                'estatus'
                            ])
                            ->whereRaw("UPPER(TRIM(descripcion)) LIKE ?", ["%$searchValue%"])
                            ->offset($iterator)
                            ->limit(5)
                            ->get();
    
        return response()->json([
            'value' => $courses
        ]);
    }
}