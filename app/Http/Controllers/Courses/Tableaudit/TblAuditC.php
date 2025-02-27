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

    public function getAuditList(Request $request)
    {
        // Llamamos al método auditlist del modelo TableauditM
        $auditModel = new TableauditM();
        $audits = $auditModel->auditlist($request->id_courses);
        
        return response()->json([
            'value' => $request->id_courses,
            'status' => true,
        ]);
    }
}

public function searchTable(Request $request)
{
    try {
        $iterator = $request->input('iterator', 1); // Página actual
        $searchValueAudit = $request->input('searchValueAudit', ''); // Valor de búsqueda

        // Validar entrada
        $request->validate([
            'iterator' => 'required|integer|min:1',
            'searchValueAudit' => 'nullable|string|max:255',
        ]);

        // Obtener resultados
        $courses = (new TableauditM())->auditlist($iterator, $searchValueAudit);

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

