<?php

namespace App\Http\Controllers\Letter\Area;

use App\Http\Controllers\Controller;
use App\Models\Letter\Area\AreainternoM;
use Illuminate\Http\Request;
use App\Http\Controllers\Letter\Log\LogC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class AreainternoC extends Controller
{
    public function __invoke()
    {
        $courses = AreainternoM::all();
        return view('administration.areainternoC.list', compact('courses'));
    }

    public function save(Request $request)
{
    $messagesC = new MessagesC();
    $logC = new LogC();

    $descripcion = strtoupper(trim($request->descripcion));
    $clave = strtoupper(trim($request->clave));

    // Validar duplicados por descripción
    $existeDescripcion = AreainternoM::whereRaw("UPPER(TRIM(descripcion)) = ?", [$descripcion])
        ->when($request->id_cat_area_interno, function ($q) use ($request) {
            return $q->where('id_cat_area_interno', '<>', $request->id_cat_area_interno);
        })
        ->exists();

    if ($existeDescripcion) {
        return redirect()->back()->withInput()->withErrors([
            'descripcion' => 'La descripción ya existe.',
        ]);
    }

    // Validar duplicados por clave
    $existeClave = AreainternoM::whereRaw("UPPER(TRIM(clave)) = ?", [$clave])
        ->when($request->id_cat_area_interno, function ($q) use ($request) {
            return $q->where('id_cat_area_interno', '<>', $request->id_cat_area_interno);
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

    if (!$request->id_cat_area_interno) {
        AreainternoM::create($data);
        $logC->add('correspondencia.cat_area_interno', $data);
    } else {
        AreainternoM::where('id_cat_area_interno', $request->id_cat_area_interno)->update($data);
        $data['id_cat_area_interno'] = $request->id_cat_area;
        $logC->edit('correspondencia.cat_area_interno', $data);
    }

    return $messagesC->messageSuccessRedirect('areainterno.list', 'Área interno guardada exitosamente.');
}


    public function create()
    {
        $item = new AreainternoM();
        return view('administration.areainternoC.form', compact('item'));
    }

   
    public function searchTable(Request $request)
    {
        $searchValue = strtoupper(trim($request->get('searchValue', '')));
        $iterator = intval($request->get('iterator', 0));
    
        $areas = AreainternoM::select([
                                'id_cat_area_interno AS id',
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
            $area = AreainternoM::findOrFail($id);
            $area->delete();
            return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el área'], 500);
        }
    }

    public function edit(string $id)
    {
        $areaM = new AreainternoM();
        $item = $areaM->edit($id);

        return view('administration.areainternoC.form', compact('item'));
    }
}