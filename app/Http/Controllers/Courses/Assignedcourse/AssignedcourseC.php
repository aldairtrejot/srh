<?php

namespace App\Http\Controllers\Courses\Assignedcourse;
use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\Assignedcourse\AssignedcourseM;

class AssignedcourseC extends Controller
{
    public function list()
{
    $assignedcourse = AssignedcourseM::all();

    // Pasar los cursos a la vista
    return view('courses/assignedcourse/list', compact('assignedcourse'));
}

public function create()
    {
        // Crear un nuevo objeto vacío para el formulario
        $item = new AssignedcourseM();
        
        return view('courses.assignedcourse.form', compact('item'));
    }

    
}




