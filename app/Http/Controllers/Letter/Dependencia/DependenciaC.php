<?php

namespace App\Http\Controllers\Letter\Dependencia;

use App\Http\Controllers\Controller;
use App\Models\Letter\Area\AreaM;
use Illuminate\Http\Request;
use App\Http\Controllers\Letter\Log\LogC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;
use App\Models\Letter\Dependencia\DependenciaM;

class DependenciaC extends Controller
{
    public function __invoke()
    {
        $courses = DependenciaM::all();
        return view('administration.dependenciaC.list', compact('courses'));
    }

    public function save(Request $request)
    {
        $messagesC = new MessagesC();
        $logC = new LogC();
    
        $descripcion = strtoupper(trim($request->descripcion));
    
        // Validar si ya existe (evitar duplicados)
        $existe = DependenciaM::whereRaw("UPPER(TRIM(descripcion)) = ?", [$descripcion])
                    ->when($request->id_cat_dependencia, function ($q) use ($request) {
                        return $q->where('id_cat_dependencia', '<>', $request->id_cat_dependencia);
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
    
        if (!$request->id_cat_dependencia) {
            DependenciaM::create($data);
            $logC->add('correspondencia.cat_dependencia', $data);
        } else {
            DependenciaM::where('id_cat_dependencia', $request->id_cat_dependencia)->update($data);
            $data['id_cat_dependencia'] = $request->id_cat_dependencia;
            $logC->edit('correspondencia.cat_dependencia', $data);
        }
    
        return $messagesC->messageSuccessRedirect('dependencia.list', 'Registro guardada exitosamente.');
    }
    

    public function create()
    {
        $item = new DependenciaM();
        return view('administration.dependenciaC.form', compact('item'));
    }

    public function searchTable(Request $request)
{
    $searchValue = strtoupper(trim($request->get('searchValue', '')));
    $iterator = intval($request->get('iterator', 0));

    $dependencia = DependenciaM::select([
                            'id_cat_dependencia AS id',
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
            $dependencia = DependenciaM::findOrFail($id);
            $dependencia->delete();
            return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el área'], 500);
        }
    }

    public function edit(string $id)
    {
        $dependenciaM = new DependenciaM();
        $item = $dependenciaM->edit($id);

        return view('administration.dependenciaC.form', compact('item'));
    }
}