<?php

namespace App\Http\Controllers\Letter\Letter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadTempC extends Controller
{
    private const MIMES = 'pdf,doc,docx,jpg,jpeg,png';
    private const MAX   = 10240; // KB = 10MB

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:' . self::MIMES . '|max:' . self::MAX,
        ], [
            'file.required' => 'Selecciona un archivo.',
            'file.mimes'    => 'Usa PDF, DOC/DOCX, JPG/PNG.',
            'file.max'      => 'El archivo no debe exceder 10MB.',
        ]);

        $file   = $request->file('file');
        $token  = (string) Str::uuid();
        $ext    = strtolower($file->getClientOriginalExtension());
        $name   = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $tmpRel = "correspondencia/tmp/{$token}.{$ext}";

        Storage::put($tmpRel, file_get_contents($file->getRealPath()));

        return response()->json([
            'ok'        => true,
            'token'     => $token,
            'filename'  => $name . '.' . $ext,
            'extension' => $ext,
            'tmp_path'  => $tmpRel,
        ]);
    }

    public function destroy(string $token)
    {
        $deleted = false;
        foreach (Storage::files('correspondencia/tmp') as $path) {
            if (str_contains($path, $token . '.')) {
                Storage::delete($path);
                $deleted = true;
            }
        }
        return response()->json(['ok' => $deleted]);
    }
}
