<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Admin\MessagesC;
use App\Models\Administration\UserM;
use App\Http\Controllers\Controller;
use App\Models\Administration\UserRoleM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserC extends Controller
{
    public function __invoke()
    {
        return view('administration/user');
    }

    public function create()
    {
        $userM = new UserM();
        $userRoleM = new UserRoleM();
        $roleOptions = collect($userRoleM->catRolList()); // Hacer que los roles sean una colección
        $item = $userM->getFillable();
        $userRoles = []; // Inicializar como arreglo vacío para crear usuario sin roles
        return view('administration.form', compact('item', 'roleOptions', 'userRoles'));
    }

    public function edit(string $id)
    {
        $userM = new UserM();
        $userRoleM = new UserRoleM();
        $item = $userM->edit($id);
        $roleOptions = $userRoleM->catRolList();
        $userRoles = $userRoleM->catRolEdit($id);
        return view('administration.form', compact('item', 'roleOptions', 'userRoles'));
    }
    public function list(Request $request)
    {
        try {

            $iterator = $request->input('iterator'); //OFSET valor de paginador
            $searchValue = $request->input('searchValue');

            $userM = new UserM();
            $value = $userM->list($iterator, $searchValue);

            return response()->json([ // Lógica para procesar la solicitud+
                'value' => $value,
                'status' => true,
            ]);

        } catch (\Exception $e) { // Manejo de errores  
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function save(Request $request)
    {
        $messagesC = new MessagesC();
        $userM = new UserM();
        $now = Carbon::now(); // Usando Carbon para la fecha actual
        $checkbox = $request->has('userEsPorNomina'); // Verifica si el checkbox está marcado

        // Validaciones comunes
        $validated = $request->validate([
            'userName' => 'required',
            'userEmail' => 'required|email',
            'userRoles' => 'required|array|min:1',
        ]);

        // Verifica si estamos creando o actualizando un usuario
        if (!isset($request->userId)) { // Creación de nuevo usuario
            $request->validate([
                'userPassword' => 'required',
                'userConfirmPassword' => 'required',
            ]);

            if ($request->userPassword !== $request->userConfirmPassword) {
                return $messagesC->messageErrorBack('Las contraseñas no coinciden');
            }

            // Validación del correo
            if (!$userM->validateEmail($request->userEmail, $request->userId)) {
                return $messagesC->messageErrorBack('Ya existe una cuenta asociada a este correo electrónico.');
            }

            // Crear usuario
            $user = $userM::create([
                'name' => $request->userName,
                'email' => $request->userEmail,
                'password' => Hash::make($request->userPassword),
                'es_por_nomina' => $checkbox,
                'estatus' => true, // Activo
                'id_usuario' => Auth::user()->id,
                'fecha_usuario' => $now,
            ]);

            // Asignar roles
            foreach ($request->userRoles as $idModRole) {
                UserRoleM::create([
                    'id' => $user->id,
                    'id_cat_modulo_rol' => $idModRole,
                ]);
            }

            return $messagesC->messageSuccessRedirect('user.list', 'Usuario añadido exitosamente.');
        } else { // Actualización de usuario
            // Validación del correo
            if (!$userM->validateEmail($request->userEmail, $request->userId)) {
                return $messagesC->messageErrorBack('Ya existe una cuenta asociada a este correo electrónico.');
            }

            // Actualizar usuario
            $userM::where('id', $request->userId)
                ->update([
                    'name' => $request->userName,
                    'email' => $request->userEmail,
                    'es_por_nomina' => $checkbox,
                    'estatus' => true, // Activo
                    'id_usuario' => Auth::user()->id,
                    'fecha_usuario' => $now,
                ]);

            // Actualizar roles
            UserRoleM::where('id', $request->userId)->delete();

            foreach ($request->userRoles as $idModRole) {
                UserRoleM::create([
                    'id' => $request->userId,
                    'id_cat_modulo_rol' => $idModRole,
                ]);
            }

            return $messagesC->messageSuccessRedirect('user.list', 'El usuario se ha modificado correctamente.');
        }
    }

    //La funcion valida que la contraseña anterior exista
    public function validatePassword(Request $request)
    {
        $userM = new UserM();
        $value = $userM->validatePassword(Auth::user()->id, $request->value);

        return response()->json([
            'value' => $value,
            'value1' => $request->value,
        ]);
    }

    //La funcion valida que la contraseña anterior exista
    public function changePassword(Request $request)
    {
        $userM = new UserM();
        $now = Carbon::now();

        // Encriptar la nueva contraseña
        $hashedPassword = Hash::make($request->value);

        // Intentar actualizar la contraseña
        $result = $userM::where('id', Auth::user()->id)
            ->update([
                'password' => $hashedPassword,  // Usar la contraseña encriptada
                'id_usuario' => Auth::user()->id,
                'fecha_usuario' => $now,
            ]);

        // Retornar verdadero si la actualización fue exitosa, falso si no
        return response()->json([
            'value' => $result > 0, // Si el resultado es mayor que 0, la actualización fue exitosa
        ]);
    }
}
