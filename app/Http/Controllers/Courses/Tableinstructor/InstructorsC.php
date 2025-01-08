<?php

namespace App\Http\Controllers\Courses\Tableinstructor;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\InstructorM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\MessagesC;
use Carbon\Carbon;

class InstructorsC extends Controller
{
    private function generateUniqueId()
    {
        return DB::table('capacitacion.tbl_instructores')->max('id_instructor') + 1;
    }

    public function list()
    {
        return view('courses.tableinstructor.list');
    }

    public function table(Request $request)
    {
        try {
            $instructorM = new InstructorM();
            $iterator = $request->input('iterator', 0);
            $searchValue = $request->input('searchValue', '');
            $roleUserArray = collect(session('SESSION_ROLE_USER'))->toArray();
            $ADM_TOTAL = config('custom_config.ADM_TOTAL');
            $COR_TOTAL = config('custom_config.COR_TOTAL');

            if (in_array($ADM_TOTAL, $roleUserArray) || in_array($COR_TOTAL, $roleUserArray)) {
                $value = $instructorM->list($iterator, $searchValue, null);
            } else {
                $value = $instructorM->list($iterator, $searchValue, Auth::id());
            }

            return response()->json(['value' => $value, 'status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function create()
    {
        $item = new InstructorM();
        $item->id_empleados = '';
        $item->uuid_constancia = '';
        $item->uuid_cv = '';
        $item->estatus_apto = '';
        $item->id_usuario_sistema = '';
        $item->fecha_usuario = '';

        return view('courses.tableinstructor.form', compact('item'));
    }

    public function edit(Request $request, string $id)
    {
        $instructorM = InstructorM::findOrFail($id); // Busca el registro
        $messagesC = new MessagesC();

        if ($request->isMethod('post')) {
            $request->validate([
                'estatus' => 'required|boolean',
            ]);

            $instructorM->estatus_apto = $request->input('estatus');
            $instructorM->save();

            return $messagesC->messageSuccessRedirect('tableinstructor.list', 'Curso actualizado exitosamente.');
        }

        return view('courses.tableinstructor.edit', compact('instructorM'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'id_empleados' => 'required|integer',
            'uuid_constancia' => 'nullable|string|max:255',
            'uuid_cv' => 'nullable|string|max:255',
            'estatus_apto' => 'nullable|integer',
            'estatus_instructor' => 'nullable|integer',
        ]);

        $now = Carbon::now();
        $messagesC = new MessagesC();

        if (!$request->id_instructor) { // Crear nuevo registro
            $data = [
                'id_instructor' => $this->generateUniqueId(),
                'id_empleados' => $request->id_empleados,
                'uuid_constancia' => $request->uuid_constancia,
                'uuid_cv' => $request->uuid_cv,
                'estatus_apto' => $request->estatus_apto,
                'estatus_instructor' => $request->estatus_instructor ?? null,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];

            InstructorM::create($data);

            return $messagesC->messageSuccessRedirect('tableinstructor.list', 'Elemento agregado con éxito.');
        } else { // Actualizar registro existente
            $data = [
                'id_empleados' => $request->id_empleados,
                'uuid_constancia' => $request->uuid_constancia,
                'uuid_cv' => $request->uuid_cv,
                'estatus_apto' => $request->estatus_apto,
                'estatus_instructor' => $request->estatus_instructor ?? null,
                'id_usuario_sistema' => Auth::user()->id,
                'fecha_usuario' => $now,
            ];

            InstructorM::where('id_instructor', $request->id_instructor)->update($data);

            return $messagesC->messageSuccessRedirect('tableinstructor.list', 'Elemento modificado con éxito.');
        }
    }
}
