<?php

namespace App\Http\Controllers\Letter\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CountReportController extends Controller
{
    public function countReportController(Request $request)
    {
        $countMin = 0; // contador minimo
        $countMax = 0; // total de registros

        return response()->json([
            'countMin' => $countMin,
            'countMax' => $countMax,
        ]);
    }
}
