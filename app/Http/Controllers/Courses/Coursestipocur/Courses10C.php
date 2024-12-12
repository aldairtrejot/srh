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
        // Validar los datos del formulario
        $request->validate([
            'descripcion' => 'required|string|max:255',
        ]);

        // Crear usuario
        $coursestipocurM::create([
            'descripcion' => $request->descripcion,
            'estatus' => $request->estatus ?? false, // Manejar estatus como false si es null
            'id_usuario_sistema' => Auth::user()->id,
            'fecha_usuario' => $now,
        ]);

        // Redirigir a la lista de cursos con un mensaje de éxito
        //return redirect()->route('coursestipocur.list')->with('success', 'Curso guardado exitosamente.');
        return $messagesC->messageSuccessRedirect('coursestipocur.list', 'Curso guardado exitosamente.');
    }

    public function create()
    {
        $item = new CoursestipocurM();
        $item->id_tipocursos = '';  // Valor por defecto
        $item->descripcion = '';    // Valor por defecto
        $item->estatus = '';     

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
}