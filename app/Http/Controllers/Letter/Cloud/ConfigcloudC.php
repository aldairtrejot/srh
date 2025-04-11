<?php

namespace App\Http\Controllers\Letter\Cloud;

use App\Http\Controllers\Controller;
use App\Models\Letter\Cloud\ConfigcloudM;
use Illuminate\Http\Request;
use App\Http\Controllers\Letter\Log\LogC;
use App\Http\Controllers\Admin\MessagesC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ConfigcloudC extends Controller
{
    public function __invoke()
    {
        $clouds = ConfigcloudM::all();
        return view('administration.configcloudC.list', compact('clouds'));
    }

    public function create()
    {
        $item = new ConfigcloudM();
        return view('administration.configcloudC.form', compact('item'));
    }

    public function save(Request $request)
    {
        $messagesC = new MessagesC();
        $logC = new LogC();

        $nombre = strtoupper(trim($request->nombre));
        $descripcion = strtoupper(trim($request->descripcion));
        $valor = strtoupper(trim($request->valor));

        // Validar duplicados por nombre
        $existeNombre = ConfigcloudM::whereRaw("UPPER(TRIM(nombre)) = ?", [$nombre])
            ->when($request->id_config_cloud, function ($q) use ($request) {
                return $q->where('id_config_cloud', '<>', $request->id_config_cloud);
            })
            ->exists();

        if ($existeNombre) {
            return redirect()->back()->withInput()->withErrors([
                'nombre' => 'El nombre ya existe.',
            ]);
        }

        $data = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'valor' => $valor,
            'estatus' => (bool) $request->estatus,
        ];

        if (!$request->id_config_cloud) {
            ConfigcloudM::create($data);
            $logC->add('correspondencia.config_cloud', $data);
        } else {
            ConfigcloudM::where('id_config_cloud', $request->id_config_cloud)->update($data);
            $data['id_config_cloud'] = $request->id_config_cloud;
            $logC->edit('correspondencia.config_cloud', $data);
        }

        return $messagesC->messageSuccessRedirect('configcloud.list', 'Configuración guardada exitosamente.');
    }

    public function edit(string $id)
    {
        $configM = new ConfigcloudM();
        $item = $configM->edit($id);

        return view('administration.configcloudC.form', compact('item'));
    }

    public function searchTable(Request $request)
    {
        $searchValue = strtoupper(trim($request->get('searchValue', '')));
        $iterator = intval($request->get('iterator', 0));

        $configM = new ConfigcloudM();
        $result = $configM->list($iterator, $searchValue, null, null);

        return response()->json([
            'value' => $result
        ]);
    }

    public function destroy($id)
    {
        try {
            $item = ConfigcloudM::findOrFail($id);
            $item->delete();
            return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar la configuración'], 500);
        }
    }
}
