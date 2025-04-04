<?php

namespace App\Http\Controllers\Letter\Area;

use App\Http\Controllers\Controller;
use App\Models\Letter\Area\AreaM;
use Illuminate\Http\Request;
use App\Http\Controllers\Letter\Log\LogC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class AreaC extends Controller
{
    public function __invoke()
    {
        $courses = AreaM::all();
        return view('administration.administrationC.list', compact('courses'));
    }

    public function save(Request $request)
    {
        $areaM = new AreaM();
        $messagesC = new MessagesC();
        $logC = new LogC();
        $now = Carbon::now();
        $data = [
            'descripcion' => $request->descripcion,
            'clave' => $request->clave,
            'estatus' => $request->estatus ?? false,
        ];
        if (!$request->id_cat_area) {
            $areaM::create($data);
            $logC->add('correspondencia.cat_area', $data);
            
        } else {
           

            $areaM::where('id_cat_area', $request->id_cat_area)->update($data);
            $data['id_cat_area'] = $request->id_cat_area;
            $logC->edit('correspondencia.cat_area', $data);
        }
        

        return $messagesC->messageSuccessRedirect('administration.list', 'Área guardada exitosamente.');
    }

    public function create()
    {
        $item = new AreaM();
        return view('administration.administrationC.form', compact('item'));
    }

   
    public function searchTable(Request $request)
    {
        $searchValue = strtoupper(trim($request->get('searchValue', '')));
        $iterator = intval($request->get('iterator', 0));
    
        $areas = AreaM::select([
                                'id_cat_area AS id',
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
            $area = AreaM::findOrFail($id);
            $area->delete();
            return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el área'], 500);
        }
    }

    public function edit(string $id)
    {
        $areaM = new AreaM();
        $item = $areaM->edit($id);

        return view('administration.administrationC.form', compact('item'));
    }
}

