<?php

namespace App\Http\Controllers\Letter\Año;

use App\Http\Controllers\Controller;
use App\Models\Letter\Año\AnioM;
use Illuminate\Http\Request;
use App\Http\Controllers\Letter\Log\LogC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class AnioC extends Controller
{
    public function __invoke()
    {
        $courses = AnioM::all();
        return view('administration.anioC.list', compact('courses'));
    }

    public function save(Request $request)
    {
        $anioM = new AnioM();
        $messagesC = new MessagesC();
        $logC = new LogC();
        $now = Carbon::now();
    
        $descripcion = strtoupper(trim($request->descripcion));
    
        // Validar si ya existe (evitar duplicados al crear)
        $existe = AnioM::whereRaw("UPPER(TRIM(descripcion)) = ?", [$descripcion])
                    ->when($request->id_cat_anio, function ($q) use ($request) {
                        return $q->where('id_cat_anio', '<>', $request->id_cat_anio); // Ignorar el mismo si es edición
                    })
                    ->exists();
    
        if ($existe) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['descripcion' => 'La descripción ya existe.']);
        }
    
        $data = [
            'descripcion' => $descripcion,
            'estatus' => $request->estatus ?? false,
        ];
    
        if (!$request->id_cat_anio) {
            $anioM::create($data);
            $logC->add('correspondencia.cat_anio', $data);
        } else {
            $anioM::where('id_cat_anio', $request->id_cat_anio)->update($data);
            $data['id_cat_anio'] = $request->id_cat_anio;
            $logC->edit('correspondencia.cat_anio', $data);
        }
    
        return $messagesC->messageSuccessRedirect('año.list', 'Registro guardado exitosamente.');
    }
    

    public function create()
    {
        $item = new AnioM();
        return view('administration.anioC.form', compact('item'));
    }

   
    public function searchTable(Request $request)
    {
        $searchValue = strtoupper(trim($request->get('searchValue', '')));
        $iterator = intval($request->get('iterator', 0));
    
        $anio = AnioM::select([
                                'id_cat_anio AS id',
                                'descripcion',
                                'estatus'
                            ])
                            ->whereRaw("UPPER(TRIM(descripcion)) LIKE ?", ["%$searchValue%"])
                            ->offset($iterator)
                            ->limit(5)
                            ->get();
    
        return response()->json([
            'value' => $anio
        ]);
    }

    public function destroy($id)
    {
        try {
            $anio = AnioM::findOrFail($id);
            $anio->delete();
            return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el área'], 500);
        }
    }

    public function edit(string $id)
    {
        $anio = new AnioM();
        $item = $anio->edit($id);

        return view('administration.anioC.form', compact('item'));
    }

}