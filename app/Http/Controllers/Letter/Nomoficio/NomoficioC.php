<?php

namespace App\Http\Controllers\Letter\Nomoficio;

use App\Http\Controllers\Controller;
use App\Models\Letter\Nomoficio\NomoficioM;
use Illuminate\Http\Request;
use App\Http\Controllers\Letter\Log\LogC;
use App\Http\Controllers\Admin\MessagesC;

class NomoficioC extends Controller
{
    public function __invoke()
    {
        $courses = NomoficioM::all();
        return view('administration.nomoficioC.list', compact('courses'));
    }

    public function save(Request $request)
    {
        $messagesC = new MessagesC();
        $logC = new LogC();

        $descripcion = strtoupper(trim($request->descripcion));
        $nombre = strtoupper(trim($request->nombre));

        $existeDescripcion = NomoficioM::whereRaw("UPPER(TRIM(descripcion)) = ?", [$descripcion])
            ->when($request->id_cat_nombre_oficio, function ($q) use ($request) {
                return $q->where('id_cat_nombre_oficio', '<>', $request->id_cat_nombre_oficio);
            })
            ->exists();

        if ($existeDescripcion) {
            return redirect()->back()->withInput()->withErrors([
                'descripcion' => 'La descripción ya existe.',
            ]);
        }

        $existeNombre = NomoficioM::whereRaw("UPPER(TRIM(nombre)) = ?", [$nombre])
            ->when($request->id_cat_nombre_oficio, function ($q) use ($request) {
                return $q->where('id_cat_nombre_oficio', '<>', $request->id_cat_nombre_oficio);
            })
            ->exists();

        if ($existeNombre) {
            return redirect()->back()->withInput()->withErrors([
                'nombre' => 'El nombre ya existe.',
            ]);
        }

        $data = [
            'descripcion' => $descripcion,
            'nombre' => $nombre,
            'estatus' => (bool) $request->estatus,
        ];

        if (!$request->id_cat_nombre_oficio) {
            NomoficioM::create($data);
            $logC->add('correspondencia.cat_nombre_oficio', $data);
        } else {
            NomoficioM::where('id_cat_nombre_oficio', $request->id_cat_nombre_oficio)->update($data);
            $data['id_cat_nombre_oficio'] = $request->id_cat_nombre_oficio;
            $logC->edit('correspondencia.cat_nombre_oficio', $data);
        }

        return $messagesC->messageSuccessRedirect('nomoficio.list', 'Registro guardado exitosamente.');
    }

    public function create()
    {
        $item = new NomoficioM();
        return view('administration.nomoficioC.form', compact('item'));
    }

    public function searchTable(Request $request)
    {
        $searchValue = strtoupper(trim($request->get('searchValue', '')));
        $iterator = intval($request->get('iterator', 0));

        $areas = NomoficioM::select([
            'id_cat_nombre_oficio AS id',
            'nombre',
            'descripcion',
            'estatus'
        ])
        ->where(function ($query) use ($searchValue) {
            $query->whereRaw("UPPER(TRIM(nombre)) LIKE ?", ["%$searchValue%"])
                  ->orWhereRaw("UPPER(TRIM(descripcion)) LIKE ?", ["%$searchValue%"]);
        })
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
            $area = NomoficioM::findOrFail($id);
            $area->delete();
            return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el área'], 500);
        }
    }

    public function edit(string $id)
    {
        $item = NomoficioM::findOrFail($id);
        return view('administration.nomoficioC.form', compact('item'));
    }
}
