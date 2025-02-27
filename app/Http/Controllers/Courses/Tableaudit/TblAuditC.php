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
    try {
        $tableauditm = new TableauditM();
        $result = $tableauditm->list();

        return response()->json([
            'status' => true,
            'data' => $result,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error al obtener la lista de auditoría',
            'error' => $e->getMessage(),
        ], 500);
    }
}
}

