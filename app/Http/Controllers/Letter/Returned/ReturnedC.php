<?php

namespace App\Http\Controllers\Letter\Returned;
use App\Http\Controllers\Controller;
use App\Models\Letter\Returned\ReturnedM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\MessagesC;
use Illuminate\Support\Facades\Log;


class ReturnedC extends Controller
{
    public function list()
    {
        return view('letter.Returned.list');
    }

    public function table(Request $request)
    {
        $searchValue = $request->get('searchValue');
        $iterator = $request->get('iterator', 0);

        $model = new ReturnedM();
        $results = $model->list($iterator, $searchValue);

        return response()->json(['value' => $results, 'status' => true]);
    }

    public function delete($id)
    {
        try {
            $item = ReturnedM::findOrFail($id);
            $item->delete();
            return response()->json(['success' => true, 'message' => 'Eliminado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar'], 500);
        }
    }

    public function create()
    {
        $item = new ReturnedM();
        $item->descripcion = '';
        $item->clave = '';
        $item->estatus = true;

        return view('letter.Returned.form', compact('item'));
    }

    public function save(Request $request)
    {
        $messagesC = new MessagesC();
        $now = Carbon::now();

        $request->validate([
            'descripcion' => 'required|string|max:255',
            'clave' => 'required|string|max:20'
        ]);

        ReturnedM::create([
            'descripcion' => $request->descripcion,
            'clave' => $request->clave,
            'estatus' => $request->estatus ?? false,
        ]);

        return $messagesC->messageSuccessRedirect('returned.list', 'Área guardada correctamente.');
    }

    public function edit(Request $request, $id)
    {
        $messagesC = new MessagesC();
        $item = ReturnedM::find($id);

        if ($request->isMethod('post')) {
            $request->validate([
                'descripcion' => 'required|string|max:255',
                'clave' => 'required|string|max:20'
            ]);

            $item->descripcion = $request->descripcion;
            $item->clave = $request->clave;
            $item->estatus = $request->estatus ?? false;
            $item->save();

            return $messagesC->messageSuccessRedirect('returned.list', 'Área actualizada correctamente.');
        }

        return view('letter.Returned.edit', compact('item'));
    }

    public function getSubareas($id)
    {
        try {
            $subareas = DB::table('correspondencia.sub_area')
                ->select('id_sub_area', 'descripcion')
                ->where('id_cat_area', $id)
                ->orderBy('descripcion', 'asc')
                ->get();

            return response()->json($subareas);
        } catch (\Exception $e) {
            \Log::error('Error al obtener subáreas: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

public function getAreaAndSubareas($id)
{
    $area = DB::table('correspondencia.cat_area')
        ->select('id_cat_area', 'descripcion')
        ->where('id_cat_area', $id)
        ->first();

    if (!$area) {
        return response()->json([
            'error' => 'Área no encontrada',
            'area' => null,
            'subareas' => []
        ]);
    }

    $subareas = DB::table('correspondencia.sub_area')
        ->select('id_sub_area', 'descripcion')
        ->where('id_cat_area', $id)
        ->orderBy('descripcion')
        ->get();

    return response()->json([
        'area' => $area,
        'subareas' => $subareas
    ]);
}


}