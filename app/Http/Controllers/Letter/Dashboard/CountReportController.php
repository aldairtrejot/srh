<?php

namespace App\Http\Controllers\Letter\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Letter\Dashboard\CountReportModel;
use Illuminate\Http\Request;

class CountReportController extends Controller
{
    public function countReportController(Request $request)
    {
        $countReportModel = new CountReportModel;
        $countMax = $countReportModel->countReportModel(); // total de registros
        $countMin = $countReportModel->countReportModelFilter($request);

        // variables de resultado
        // Leyenda de contador
        $lab_contador = $countMin.' de '.$countMax;

        return response()->json([
            'lab_contador' => $lab_contador,

        ]);
    }
}
