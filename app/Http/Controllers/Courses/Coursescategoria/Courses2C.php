<?php

namespace App\Http\Controllers\Courses\Coursescategoria;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\CoursescategoriaM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class Courses2C extends Controller
{
    public function __invoke()
{
    // Obtener todos los cursos
    $coursescategoria = CoursescategoriaM::all();

    // Pasar los cursos a la vista
    return view('courses/coursescategoria/list', compact('coursescategoria'));
}

public function save(Request $request)
{
    $coursescategoriaM = new CoursescategoriaM();
    $messagesC = new MessagesC();
    $now = Carbon::now(); // Usando Carbon para la fecha actual

    if (!$request->id_cat_categoria) {
        // Crear nuevo curso
        $nuevoCurso = $coursescategoriaM::create([
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

        $coursescategoriaM::where('id_cat_categoria', $request->id_cat_categoria)->update($data);
    }

    // Redirigir con mensaje de éxito
    return $messagesC->messageSuccessRedirect('coursescategoria.list', 'Curso guardado exitosamente.');
}

    public function create()
    {
        $item = new CoursescategoriaM();  

        return view('courses.coursescategoria.form', compact('item'));
    }
    public function searchTable(Request $request)
    {
        $searchValue = $request->get('searchValue');  // Término de búsqueda
        $iterator = $request->get('iterator', 0);  // Si no se pasa iterador, por defecto será 0 (primera página)

        // Filtrar los cursos que coincidan con la búsqueda
        $courses = CoursescategoriaM::where('descripcion', 'like', '%' . $searchValue . '%')
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
            $course = CoursescategoriaM::findOrFail($id);
                $course->delete();
                return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']); 
            } catch (\Exception $e) {
                return response()->json(['error' => 'Error al eliminar el curso'], 500);
                
            }
            
    }
    public function edit(string $id)
    {
        $coursescategoriaM = new CoursescategoriaM();
        $item = $coursescategoriaM ->edit($id);

        return view('courses.coursescategoria.form', compact('item'));
       
    }
}