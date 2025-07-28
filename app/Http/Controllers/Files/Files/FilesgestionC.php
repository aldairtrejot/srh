<?php

namespace App\Http\Controllers\Files\Files;

use App\Http\Controllers\Controller;
use App\Models\Files\Files\FilesgestionM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\Admin\MessagesC;

class FilesgestionC extends Controller
{
    /**
     * Carga la vista principal de gestión de documentos.
     */
    public function __invoke()
    {
        // Puedes pasar datos como catálogos si lo necesitas
        return view('files.filesgestion.list');
    }

    /**
     * Realiza la búsqueda paginada de los registros.
     */
    public function searchTable(Request $request)
    {
        $searchValue = strtoupper(trim($request->input('search', '')));
        $page = (int) $request->input('page', 1);
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $model = new FilesgestionM();
        $results = $model->list($offset, $searchValue, $limit);  // nota: limit como parámetro
        $total = $model->count($searchValue); // método count personalizado del modelo

        return response()->json([
            'data' => $results,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'status' => true,
        ]);
    }

    /**
     * Elimina un registro por ID.
     */
    public function destroy($id)
    {
        $messagesC = new MessagesC();

        try {
            $model = new FilesgestionM();
            $record = $model->find($id);

            if (!$record) {
                return response()->json(['error' => 'Registro no encontrado'], 404);
            }

            $record->delete();

            return response()->json(['success' => true, 'message' => 'Registro eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el registro'], 500);
        }
    }
}

