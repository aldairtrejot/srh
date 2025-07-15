<?php

namespace App\Http\Controllers\Files\Files;

use App\Http\Controllers\Controller;
use App\Models\Files\Files\FilesdocumentM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class FilesdocumentC extends Controller
{
    public function __invoke()
    {
        $items = FilesdocumentM::all();
        return view('files.filesdocument.list', compact('items'));
    }

    public function save(Request $request)
    {
        $messagesC = new MessagesC();
        $now = Carbon::now();

        $id = $request->input('id_cat_documento');
        $descripcion = strtoupper(trim($request->input('descripcion')));
        $estatus = $request->has('estatus') ? true : false;

        if ($id) {
            $document = FilesdocumentM::find($id);

            if ($document) {
                $document->descripcion = $descripcion;
                $document->estatus = $estatus;
                $document->id_modificacion = Auth::id();
                $document->actualizado_en = $now;
                $document->save();
            } else {
                return $messagesC->messageErrorRedirect('filesdocument.list', 'No se encontró el documento para editar.');
            }
        } else {
            FilesdocumentM::create([
                'descripcion' => $descripcion,
                'estatus' => $estatus,
                'id_usuario_creacion' => Auth::id(),
                'id_modificacion' => Auth::id(),
                'creado_en' => $now,
                'actualizado_en' => $now
            ]);
        }

        return $messagesC->messageSuccessRedirect('filesdocument.list', 'Registro guardado exitosamente.');
    }

    public function create()
    {
        $item = new FilesdocumentM();  
        return view('files.filesdocument.form', compact('item'));
    }

    public function searchTable(Request $request)
    {
        $searchValue = strtoupper(trim($request->get('searchValue')));
        $iterator = $request->get('iterator', 0);

        $results = FilesdocumentM::whereRaw('UPPER(TRIM(descripcion)) LIKE ?', ['%' . $searchValue . '%'])
            ->offset($iterator)
            ->limit(5)
            ->get();

        return response()->json([
            'value' => $results,
            'status' => true,
        ]);
    }

    public function destroy($id)
    {
        try {
            $doc = FilesdocumentM::findOrFail($id);
            $doc->delete();
            return response()->json(['success' => true, 'message' => 'Eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el documento'], 500);
        }
    }

    public function edit(string $id)
    {
        $filesdocumentM = new FilesdocumentM();
        $item = $filesdocumentM->edit($id);
        return view('files.filesdocument.form', compact('item'));
    }
}


