<?php

namespace App\Http\Controllers\Courses\Tableinstructor;

use App\Http\Controllers\Controller;
use App\Models\Courses\Courses\Instructores\Instructores\InstructorM;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\MessagesC;

class InstructorsC extends Controller
{
    public function __invoke()
    {
        $tableInstructors = InstructorM::all();
        return view('courses.tableinstructor.list', compact('tableInstructors'));
    }

    public function save(Request $request)
    {
        $instructorM = new InstructorM();
        $messagesC = new MessagesC();
        $now = Carbon::now();

        $request->validate([
            'estatus' => 'required|boolean',
        ]);

        $instructorM::create([
            'estatus' => $request->estatus,
            'id_usuario_sistema' => Auth::id(),
            'fecha_usuario' => $now,
        ]);

        return $messagesC->messageSuccessRedirect('tableinstructor.list', 'Instructor guardado exitosamente.');
    }

    public function create()
    {
        $item = new InstructorM();
        $item->estatus = '';
        return view('courses.tableinstructor.form', compact('item'));
    }

    public function searchTable(Request $request)
    {
        $searchValue = $request->get('searchValue', '');
        $iterator = max(0, (int)$request->get('iterator', 0));

        if (empty($searchValue)) {
            return response()->json([
                'value' => [],
                'status' => true,
                'message' => 'Sin resultados para el término de búsqueda.',
            ]);
        }

        $instructors = InstructorM::where('estatus', 'LIKE', '%' . $searchValue . '%')
            ->offset($iterator)
            ->limit(5)
            ->get();

        return response()->json([
            'value' => $instructors,
            'status' => true,
        ]);
    }

    public function destroy($id)
    {
        try {
            $instructor = InstructorM::findOrFail($id);
            $instructor->delete();

            return response()->json(['success' => true, 'message' => 'Instructor eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el instructor.'], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        $instructor = InstructorM::find($id);
        $messagesC = new MessagesC();

        if (!$instructor) {
            abort(404, 'Instructor no encontrado.');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'estatus' => 'required|boolean',
            ]);

            $instructor->estatus = $request->input('estatus') ? true : false;
            $instructor->save();

            return $messagesC->messageSuccessRedirect('tableinstructor.list', 'Instructor actualizado exitosamente.');
        }

        return view('courses.tableinstructor.edit', compact('instructor'));
    }
}
