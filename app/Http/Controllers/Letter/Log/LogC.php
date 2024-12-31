<?php

namespace App\Http\Controllers\Letter\log;

use App\Http\Controllers\Controller;
use App\Models\Letter\Log\LogM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LogC extends Controller
{
    public function add($tableName, $data)
    {
        $now = Carbon::now(); //Hora y fecha actual
        $dataJson = json_encode($data);
        $logM = new LogM();
        $logM::create([
            'nombre_tabla' => $tableName,
            'accion' => 'AGREGAR',
            'valores' => $dataJson,
            'id_usuario' => Auth::user()->id,
            'fecha' => $now,
        ]);
    }

    public function edit($tableName, $data)
    {
        $now = Carbon::now(); //Hora y fecha actual
        $dataJson = json_encode($data);
        $logM = new LogM();
        $logM::create([
            'nombre_tabla' => $tableName,
            'accion' => 'MODIFICAR',
            'valores' => $dataJson,
            'id_usuario' => Auth::user()->id,
            'fecha' => $now,
        ]);
    }

    public function delete($tableName, $data)
    {
        $now = Carbon::now(); //Hora y fecha actual
        $dataJson = json_encode($data);
        $logM = new LogM();
        $logM::create([
            'nombre_tabla' => $tableName,
            'accion' => 'ELIMINAR',
            'valores' => $dataJson,
            'id_usuario' => Auth::user()->id,
            'fecha' => $now,
        ]);
    }
}

