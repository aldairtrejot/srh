<?php

namespace App\Http\Controllers\Letter\Letter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Letter\Letter\LetterM;

class ReturnadoC extends Controller
{
    public function check(Request $request)
    {
        try {
            $m  = new LetterM();
            $a1 = (int) $request->input('a1', 0);
            $a2 = (int) $request->input('a2', 0);
            $a3 = (int) $request->input('a3', 0);

            $rid  = $m->getReturnadoId();
            $has  = [
                'a1' => $m->areaHasReturnado($a1),
                'a2' => $m->areaHasReturnado($a2),
                'a3' => $m->areaHasReturnado($a3),
            ];
            $only = [
                'a1' => $m->areaOnlyReturnado($a1),
                'a2' => $m->areaOnlyReturnado($a2),
                'a3' => $m->areaOnlyReturnado($a3),
            ];

            return response()->json([
                'ok'                 => true,
                'idReturnado'        => $rid,
                'has'                => $has,
                'only'               => $only,
                'any_has_returnado'  => in_array(true, $has, true),
                'any_only_returnado' => in_array(true, $only, true),
            ]);
        } catch (\Throwable $e) {
            \Log::error('RETURNADO_CHECK_ERROR: '.$e->getMessage(), ['ex' => $e]);
            return response()->json(['ok' => false, 'message' => 'Error interno'], 500);
        }
    }
}
