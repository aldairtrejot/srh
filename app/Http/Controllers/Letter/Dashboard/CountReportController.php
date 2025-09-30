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
        $countMin = $countMax;

        return response()->json([
            'countMin' => $countMin,
            'countMax' => $countMax,
        ]);
    }
}
