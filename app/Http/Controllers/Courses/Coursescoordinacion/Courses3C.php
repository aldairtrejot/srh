<?php

namespace App\Http\Controllers\Courses\Coursescoordinacion;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\CoursescoordinacionM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class Courses3C extends Controller
{
    public function __invoke()
    {
        // Obtener todos los cursos de coordinación
        $coursescoordinacion = CoursescoordinacionM::all();

        // Pasar los cursos a la vista
        return view('courses/coursescoordinacion/list', compact('coursescoordinacion'));
    }

    public function save(Request $request)
    {
        $coursescoordinacionM = new CoursescoordinacionM();
        $messagesC = new MessagesC();
        $now = Carbon::now(); // Usando Carbon para la fecha actual
    
        if (!$request->id_cat_coordinacion) {
            // Crear nuevo curso
            $nuevoCurso = $coursescoordinacionM::create([
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
    
            $coursescoordinacionM::where('id_cat_coordinacion', $request->id_cat_coordinacion)->update($data);
        }
    
        // Redirigir con mensaje de éxito
        return $messagesC->messageSuccessRedirect('coursescoordinacion.list', 'Curso guardado exitosamente.');
    }
    

    public function create()
    {
        $item = new CoursescoordinacionM(); 

        return view('courses.coursescoordinacion.form', compact('item'));
    }
    

    public function destroy($id)
    {   
           try {
            $course = CoursescoordinacionM::findOrFail($id);
                $course->delete();
                return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']); 
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al eliminar el curso'], 500);
                
            }
            
    }
    public function edit(string $id)
    {
        $coursescoordinacionM = new CoursescoordinacionM();
        $item = $coursescoordinacionM ->edit($id);

        return view('courses.coursescoordinacion.form', compact('item'));
       
    }
    public function searchTable(Request $request)
    {
        $searchValue = strtoupper(trim($request->get('searchValue', '')));
        $iterator = intval($request->get('iterator', 0));
    
        $courses = CoursescoordinacionM::select([
                                'id_cat_coordinacion AS id',
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