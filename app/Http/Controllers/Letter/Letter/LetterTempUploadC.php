<?php

namespace App\Http\Controllers\Letter\Letter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class LetterTempUploadC extends Controller
{
    private string $tmpDisk = 'local';
    private string $tmpDir  = 'tmp_letters';

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => [
                'required',
                'file',
                'max:10240', // 10 MB
                'mimes:pdf,doc,docx,jpg,jpeg,png'
            ],
        ], [
            'file.required' => 'Debes seleccionar un archivo.',
            'file.max'      => 'El archivo no debe exceder los 10MB.',
            'file.mimes'    => 'Formatos permitidos: PDF, DOC, DOCX, JPG, JPEG, PNG.',
        ]);

        if ($validator->fails()) {
            return response()->json(['ok' => false, 'errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $token = Str::uuid()->toString();
        $ext = $file->getClientOriginalExtension();
        $name = $file->getClientOriginalName();

        Storage::disk($this->tmpDisk)->putFileAs($this->tmpDir, $file, "{$token}.{$ext}");

        return response()->json([
            'ok'       => true,
            'token'    => $token,
            'filename' => $name,
            'ext'      => $ext,
            'path'     => "{$this->tmpDir}/{$token}.{$ext}",
        ]);
    }

    public function destroy(string $token)
    {
        $files = Storage::disk($this->tmpDisk)->files($this->tmpDir);
        foreach ($files as $f) {
            if (str_contains($f, $token)) {
                Storage::disk($this->tmpDisk)->delete($f);
            }
        }
        return response()->json(['ok' => true]);
    }

    // Helpers para uso interno desde LetterC@save si los necesitas
    public function fetchTempFile(string $token): ?array
    {
        $files = Storage::disk($this->tmpDisk)->files($this->tmpDir);
        foreach ($files as $f) {
            if (str_contains($f, $token)) {
                return [
                    'path' => $f,
                    'stream' => Storage::disk($this->tmpDisk)->readStream($f),
                    'name' => basename($f),
                ];
            }
        }
        return null;
    }

    public function dropTempByToken(string $token): void
    {
        $files = Storage::disk($this->tmpDisk)->files($this->tmpDir);
        foreach ($files as $f) {
            if (str_contains($f, $token)) {
                Storage::disk($this->tmpDisk)->delete($f);
            }
        }
    }
}
