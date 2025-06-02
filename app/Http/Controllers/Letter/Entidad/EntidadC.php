<?php

namespace App\Http\Controllers\Letter\Entidad;

use App\Http\Controllers\Controller;
use App\Models\Letter\Entidad\EntidadM;
use Illuminate\Http\Request;
use App\Http\Controllers\Letter\Log\LogC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class EntidadC extends Controller
{
    public function __invoke()
    {
        $courses = EntidadM::all();
        return view('administration.entidadC.list', compact('courses'));
    }

    public function save(Request $request)
{
    $messagesC = new MessagesC();
    $logC = new LogC();

    $descripcion = strtoupper(trim($request->descripcion));
    $clave = strtoupper(trim($request->clave));

    // Validar duplicados por descripción
    $existeDescripcion = EntidadM::whereRaw("UPPER(TRIM(descripcion)) = ?", [$descripcion])
        ->when($request->id_cat_entidad, function ($q) use ($request) {
            return $q->where('id_cat_entidad', '<>', $request->id_cat_entidad);
        })
        ->exists();

    if ($existeDescripcion) {
        return redirect()->back()->withInput()->withErrors([
            'descripcion' => 'La descripción ya existe.',
        ]);
    }

    // Validar duplicados por clave
    $existeClave = EntidadM::whereRaw("UPPER(TRIM(clave)) = ?", [$clave])
        ->when($request->id_cat_entidad, function ($q) use ($request) {
            return $q->where('id_cat_entidad', '<>', $request->id_cat_entidad);
        })
        ->exists();

    if ($existeClave) {
        return redirect()->back()->withInput()->withErrors([
            'clave' => 'La clave ya existe.',
        ]);
    }

    $data = [
        'descripcion' => $descripcion,
        'clave' => $clave,
        'estatus' => (bool) $request->estatus,
    ];

    if (!$request->id_cat_entidad) {
        EntidadM::create($data);
        $logC->add('correspondencia.cat_entidad', $data);
    } else {
        EntidadM::where('id_cat_entidad', $request->id_cat_entidad)->update($data);
        $data['id_cat_entidad'] = $request->id_cat_entidad;
        $logC->edit('correspondencia.cat_entidad', $data);
    }

    return $messagesC->messageSuccessRedirect('entidad.list', 'Entidad guardada exitosamente.');
}


    public function create()
    {
        $item = new EntidadM();
        return view('administration.entidadC.form', compact('item'));
    }

   
    public function searchTable(Request $request)
    {
        $searchValue = strtoupper(trim($request->get('searchValue', '')));
        $iterator = intval($request->get('iterator', 0));
    
        $areas = EntidadM::select([
                                'id_cat_entidad AS id',
                                'descripcion',
                                'clave',
                                'estatus'
                            ])
                            ->whereRaw("UPPER(TRIM(descripcion)) LIKE ?", ["%$searchValue%"])
                            ->orwhereRaw("UPPER(TRIM(clave)) LIKE ?", ["%$searchValue%"])
                            ->offset($iterator)
                            ->limit(5)
                            ->get();
    
        return response()->json([
            'value' => $areas
        ]);
    }

    public function destroy($id)
    {
        try {
            $area = EntidadM::findOrFail($id);
            $area->delete();
            return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el área'], 500);
        }
    }

    public function edit(string $id)
    {
        $areaM = new EntidadM();
        $item = $areaM->edit($id);

        return view('administration.entidadC.form', compact('item'));
    }
}