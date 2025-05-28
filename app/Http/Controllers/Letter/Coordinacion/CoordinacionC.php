<?php

namespace App\Http\Controllers\Letter\Coordinacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Letter\Log\LogC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;
use App\Models\Letter\Coordinacion\CoordinacionM;

class CoordinacionC extends Controller
{
    public function __invoke()
    {
        $courses = CoordinacionM::all();
        return view('administration.coordinacionC.list', compact('courses'));
    }

    public function save(Request $request)
    {
        $messagesC = new MessagesC();
        $logC = new LogC();
    
        $descripcion = strtoupper(trim($request->descripcion));
    
        // Validar si ya existe (evitar duplicados)
        $existe = CoordinacionM::whereRaw("UPPER(TRIM(descripcion)) = ?", [$descripcion])
                    ->when($request->id_cat_coordinacion, function ($q) use ($request) {
                        return $q->where('id_cat_coordinacion', '<>', $request->id_cat_coordinacion);
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
    
        if (!$request->id_cat_coordinacion) {
            CoordinacionM::create($data);
            $logC->add('correspondencia.cat_coordinacion', $data);
        } else {
            CoordinacionM::where('id_cat_coordinacion', $request->id_cat_coordinacion)->update($data);
            $data['id_cat_coordinacion'] = $request->id_cat_coordinacion;
            $logC->edit('correspondencia.cat_coordinacion', $data);
        }
    
        return $messagesC->messageSuccessRedirect('coordinacion.list', 'Registro guardada exitosamente.');
    }
    

    public function create()
    {
        $item = new CoordinacionM();
        return view('administration.coordinacionC.form', compact('item'));
    }

    public function searchTable(Request $request)
{
    $searchValue = strtoupper(trim($request->get('searchValue', '')));
    $iterator = intval($request->get('iterator', 0));

    $dependencia = CoordinacionM::select([
                            'id_cat_coordinacion AS id',
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
            $dependencia = CoordinacionM::findOrFail($id);
            $dependencia->delete();
            return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el área'], 500);
        }
    }

    public function edit(string $id)
    {
        $dependenciaM = new CoordinacionM();
        $item = $dependenciaM->edit($id);

        return view('administration.coordinacionC.form', compact('item'));
    }
}