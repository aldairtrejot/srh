<?php

namespace App\Http\Controllers\Letter\Dependencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Letter\Log\LogC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;
use App\Models\Letter\Dependencia\ReldependenciaM;

class ReldependenciaC extends Controller
{
    public function __invoke()
    {
        $courses = ReldependenciaM::all();
        return view('administration.reldependenciaC.list', compact('courses'));
    }

    public function searchTable(Request $request)
{
    $searchValue = strtoupper(trim($request->get('searchValue', '')));
    $iterator = intval($request->get('iterator', 0));

    $reldependencia = ReldependenciaM::select([
                            'id_cat_dependencia AS dependencia',
                            'id_cat_dependencia_area AS area'
                        ])
                        ->whereRaw("UPPER(TRIM(id_cat_dependencia)) LIKE ?", ["%$searchValue%"])
                        ->orwhereRaw("UPPER(TRIM(id_cat_dependencia_area)) LIKE ?", ["%$searchValue%"])
                        ->offset($iterator)
                        ->limit(5)
                        ->get();

    return response()->json([
        'value' => $reldependencia
    ]);
}

}