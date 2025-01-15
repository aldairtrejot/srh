<?php

namespace App\Http\Controllers\Courses\Tablecourses;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\TblcoursesM;
use Illuminate\Http\Request;

class TblCoursesC extends Controller
{
    public function __invoke()
    {
        return view('courses/tablecourses/list');
    }

    public function searchTable(Request $request)
    {
        try {
            $iterator = $request->input('iterator', 1); // Página actual
            $searchValue = $request->input('searchValue', ''); // Valor de búsqueda

            // Validar entrada
            $request->validate([
                'iterator' => 'required|integer|min:1',
                'searchValue' => 'nullable|string|max:255',
            ]);

            // Obtener resultados
            $courses = (new TblcoursesM())->list($iterator, $searchValue);

            return response()->json([
                'success' => true,
                'message' => 'Resultados obtenidos correctamente',
                'data' => $courses->items(),
                'pagination' => [
                    'current_page' => $courses->currentPage(),
                    'last_page' => $courses->lastPage(),
                    'per_page' => $courses->perPage(),
                    'total' => $courses->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

