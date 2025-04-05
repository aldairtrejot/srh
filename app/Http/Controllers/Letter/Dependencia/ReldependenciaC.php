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
    public function searchTable(Request $request)
    {
        $iterator = $request->input('iterator', 0);
        $searchValue = $request->input('searchValue', '');

        $model = new ReldependenciaM();
        $data = $model->list($iterator, $searchValue);

        return response()->json([
            'value' => $data
        ]);
    }

    // Si también estás usando esta vista como index
    public function __invoke()
    {
        return view('administration.reldependenciaC.list');
    }
    

}