<?php

namespace App\Http\Controllers\Letter\Estatus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Letter\Log\LogC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;
use App\Models\Letter\Estatus\EstatusM;

class EstatusC extends Controller
{
    public function __invoke()
    {
        $courses = EstatusM::all();
        return view('administration.estatusC.list', compact('courses'));
    }

    public function save(Request $request)
    {
        $messagesC = new MessagesC();
        $logC = new LogC();
    
        $descripcion = strtoupper(trim($request->descripcion));
    
        // Validar si ya existe (evitar duplicados)
        $existe = EstatusM::whereRaw("UPPER(TRIM(descripcion)) = ?", [$descripcion])
                    ->when($request->id_cat_estatus, function ($q) use ($request) {
                        return $q->where('id_cat_estatus', '<>', $request->id_cat_estatus);
                    })
                    ->exists();
    
        if ($existe) {
            return redirect()->back()->withInput()->withErrors([
                'descripcion' => 'La descripción ya existe.',
            ]);
        }
    
        $data = [
            'descripcion' => $descripcion,
            'estatus' => (bool) $request->estatus,
        ];
    
        if (!$request->id_cat_estatus) {
            EstatusM::create($data);
            $logC->add('correspondencia.cat_estatus', $data);
        } else {
            EstatusM::where('id_cat_estatus', $request->id_cat_estatus)->update($data);
            $data['id_cat_estatus'] = $request->id_cat_estatus;
            $logC->edit('correspondencia.cat_estatus', $data);
        }
    
        return $messagesC->messageSuccessRedirect('estatus.list', 'Registro guardada exitosamente.');
    }
    

    public function create()
    {
        $item = new EstatusM();
        return view('administration.estatusC.form', compact('item'));
    }

    public function searchTable(Request $request)
{
    $searchValue = strtoupper(trim($request->get('searchValue', '')));
    $iterator = intval($request->get('iterator', 0));

    $dependencia = EstatusM::select([
                            'id_cat_estatus AS id',
                            'descripcion',
                            'estatus'
                        ])
                        ->whereRaw("UPPER(TRIM(descripcion)) LIKE ?", ["%$searchValue%"])
                        ->offset($iterator)
                        ->limit(5)
                        ->get();

    return response()->json([
        'value' => $dependencia
    ]);
}


    public function destroy($id)
    {
        try {
            $dependencia = EstatusM::findOrFail($id);
            $dependencia->delete();
            return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el área'], 500);
        }
    }

    public function edit(string $id)
    {
        $dependenciaM = new EstatusM();
        $item = $dependenciaM->edit($id);

        return view('administration.estatusC.form', compact('item'));
    }
}