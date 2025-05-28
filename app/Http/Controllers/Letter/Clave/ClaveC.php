<?php

namespace App\Http\Controllers\Letter\Clave;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Letter\Log\LogC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;
use App\Models\Letter\Clave\ClaveM;

class ClaveC extends Controller
{
    public function __invoke()
    {
        $courses = ClaveM::all();
        return view('administration.claveC.list', compact('courses'));
    }

    public function save(Request $request)
    {
        $messagesC = new MessagesC();
        $logC = new LogC();
    
        $descripcion = strtoupper(trim($request->descripcion));
    
        // Validar si ya existe (evitar duplicados)
        $existe = ClaveM::whereRaw("UPPER(TRIM(descripcion)) = ?", [$descripcion])
                    ->when($request->id_cat_clave, function ($q) use ($request) {
                        return $q->where('id_cat_clave', '<>', $request->id_cat_clave);
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
    
        if (!$request->id_cat_clave) {
            ClaveM::create($data);
            $logC->add('correspondencia.cat_clave', $data);
        } else {
            ClaveM::where('id_cat_clave', $request->id_cat_clave)->update($data);
            $data['id_cat_clave'] = $request->id_cat_clave;
            $logC->edit('correspondencia.cat_clave', $data);
        }
    
        return $messagesC->messageSuccessRedirect('clave.list', 'Registro guardada exitosamente.');
    }
    

    public function create()
    {
        $item = new ClaveM();
        return view('administration.claveC.form', compact('item'));
    }

    public function searchTable(Request $request)
{
    $searchValue = strtoupper(trim($request->get('searchValue', '')));
    $iterator = intval($request->get('iterator', 0));

    $dependencia = ClaveM::select([
                            'id_cat_clave AS id',
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
            $dependencia = ClaveM::findOrFail($id);
            $dependencia->delete();
            return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el área'], 500);
        }
    }

    public function edit(string $id)
    {
        $dependenciaM = new ClaveM();
        $item = $dependenciaM->edit($id);

        return view('administration.claveC.form', compact('item'));
    }
}