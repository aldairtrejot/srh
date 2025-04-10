<?php

namespace App\Http\Controllers\Letter\Dependencia;

use App\Http\Controllers\Controller;
use App\Models\Letter\Area\AreaM;
use Illuminate\Http\Request;
use App\Http\Controllers\Letter\Log\LogC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;
use App\Models\Letter\Dependencia\DependenciareaM;

class DependenciareaC extends Controller
{
    public function __invoke()
    {
        $courses = DependenciareaM::all();
        return view('administration.dependenciareaC.list', compact('courses'));
    }

    public function save(Request $request)
    {
        $messagesC = new MessagesC();
        $logC = new LogC();
    
        $descripcion = strtoupper(trim($request->descripcion));
    
        // Validar si ya existe (evitar duplicados)
        $existe = DependenciareaM::whereRaw("UPPER(TRIM(descripcion)) = ?", [$descripcion])
                    ->when($request->id_cat_dependencia_area, function ($q) use ($request) {
                        return $q->where('id_cat_dependencia_area', '<>', $request->id_cat_dependencia_area);
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
    
        if (!$request->id_cat_dependencia_area) {
            DependenciareaM::create($data);
            $logC->add('correspondencia.cat_dependencia_area', $data);
        } else {
            DependenciareaM::where('id_cat_dependencia_area', $request->id_cat_dependencia_area)->update($data);
            $data['id_cat_dependencia_area'] = $request->id_cat_dependencia_area;
            $logC->edit('correspondencia.cat_dependencia_area', $data);
        }
    
        return $messagesC->messageSuccessRedirect('dependenciarea.list', 'Dependencia guardada exitosamente.');
    }
    

    public function create()
    {
        $item = new DependenciareaM();
        return view('administration.dependenciareaC.form', compact('item'));
    }

    public function searchTable(Request $request)
{
    $searchValue = strtoupper(trim($request->get('searchValue', '')));
    $iterator = intval($request->get('iterator', 0));

    $dependenciarea = DependenciareaM::select([
                            'id_cat_dependencia_area AS id',
                            'descripcion',
                            'estatus'
                        ])
                        ->whereRaw("UPPER(TRIM(descripcion)) LIKE ?", ["%$searchValue%"])
                        ->offset($iterator)
                        ->limit(5)
                        ->get();

    return response()->json([
        'value' => $dependenciarea
    ]);
}


    public function destroy($id)
    {
        try {
            $dependenciarea = DependenciareaM::findOrFail($id);
            $dependenciarea->delete();
            return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el área'], 500);
        }
    }

    public function edit(string $id)
    {
        $dependenciareaM = new DependenciareaM();
        $item = $dependenciareaM->edit($id);

        return view('administration.dependenciareaC.form', compact('item'));
    }
}