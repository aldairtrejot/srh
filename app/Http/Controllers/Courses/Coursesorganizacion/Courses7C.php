<?php

namespace App\Http\Controllers\Courses\Coursesorganizacion;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\CoursesorganizacionM;
use Illuminate\Http\Request;

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
        // Validar los datos del formulario
        $request->validate([
            'descripcion' => 'required|string|max:255',
        ]);

        // Crear usuario
        $coursesorganizacionM::create([
            'descripcion' => $request->descripcion,
            'estatus' => $request->estatus ?? false, // Manejar estatus como false si es null
        ]);

        // Redirigir a la lista de cursos con un mensaje de éxito
        return redirect()->route('coursesorganizacion.list')->with('success', 'Curso guardado exitosamente.');
    }

    public function create()
    {
        $item = new CoursesorganizacionM();
        $item->id_organizacion = '';  // Valor por defecto
        $item->descripcion = '';    // Valor por defecto
        $item->estatus = '';     

        return view('courses.coursesorganizacion.form', compact('item'));
    }
}