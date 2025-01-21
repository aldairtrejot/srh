<?php

namespace App\Models\Courses\Courses\Cursoinstructor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CourseinstructorM extends Model{
    public function cursoinstructor(string $name)
    {
        $query = DB::table('capacitacion.tbl_cursos')
                   ->select('id_tbl_cursos') 
                   ->whereRaw('TRIM(UPPER(programa_proyecto)) = ?', [strtoupper(trim($name))]) // Aplicamos TRIM y UPPER al filtro
                   ->first();
        
        // Retornamos o usamos el resultado según sea necesario
        if ($query) {
            // Acciones con el resultado
            echo "Curso encontrado: " . $query->id_tbl_cursos;
        } else {
            echo "No se encontró el curso.";
        }

        // Retornamos el usuario o null si no se encuentra
        return $query ?? null;
    }
}