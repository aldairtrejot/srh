<?php

namespace App\Http\Controllers\Courses\Tableaudit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Courses\Courses\Tableaudit\TableauditM; 

class TblAuditC extends Controller
{
   
    public function storeAudit(Request $request)
    {
        $tableauditm = new TableauditM();
       $result = $tableauditm->list($request->id_courses);

        return response()->json([
            'value' => $request->id_courses,
            'status' => true,
        ]);
    }
    public function searchTable(Request $request)
    {
        try {
            // Validar entrada antes de obtener los valores
            $validatedData = $request->validate([
                'iterator' => 'required|integer|min:1',
                'searchValue' => 'nullable|string|max:255',
            ]);
    
            $iterator = $validatedData['iterator'];
            $searchValue = $validatedData['searchValue'] ?? '';
    
            // Obtener resultados
            $courses = (new TableauditM())->listaudit($iterator);
    
            // Responder con los resultados
            return response()->json([
                'value' => $courses, // Devuelve los cursos en el formato esperado
                'status' => true,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
   
}

