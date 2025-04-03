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
        $dependenciaM = new DependenciaM();
        $messagesC = new MessagesC();
        $logC = new LogC();
        $now = Carbon::now();
        $data = [
            'descripcion' => $request->descripcion,
            'estatus' => $request->estatus ?? false,
        ];
        if (!$request->id_cat_dependencia) {
            $dependenciaM::create($data);
            $logC->add('correspondencia.cat_dependencia', $data);
            
        } else {
           

            $dependenciaM::where('id_cat_dependencia', $request->id_cat_dependencia)->update($data);
            $data['id_cat_dependencia'] = $request->id_cat_dependencia;
            $logC->edit('correspondencia.cat_dependencia', $data);
        }
        

        return $messagesC->messageSuccessRedirect('dependencia.list', 'Dependencia guardada exitosamente.');
    }

    public function create()
    {
        $item = new DependenciaM();
        return view('administration.dependenciaC.form', compact('item'));
    }

    public function searchTable(Request $request)
    {
        $searchValue = $request->get('searchValue');
        $iterator = $request->get('iterator', 0);

        $dependencia = DependenciaM::where('descripcion', 'like', '%' . $searchValue . '%')
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