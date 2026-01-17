<?php

namespace App\Models\Administration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
class UserM extends Model
{

    protected $table = 'administration.users';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'id_tbl_empleados_hraes',
        'id_tbl_empleados_central',
        'id_tbl_empleados_trasnferidos',
        'id_tbl_empleados_aux',
        'es_por_nomina',
        'estatus',
        'id_usuario',
        'fecha_usuario'
    ];
    public function list($iterator, $searchValue)
    {
        // Comienza la consulta base
        $query = DB::table('administration.users')
            ->select(
                'id',
                DB::raw('UPPER(name) AS name'),
                'email'
            )
            ->orderBy('id', 'DESC')
            ->offset($iterator)
            ->limit(5);  // Limitar los resultados a 5 con offset (paginación)

        // Si se proporciona un valor de búsqueda, agregamos la cláusula WHERE
        if (!empty($searchValue)) {
            $query->where(function ($query) use ($searchValue) {
                $query->whereRaw('UPPER(TRIM(name)) LIKE ?', ['%' . strtoupper(trim($searchValue)) . '%'])
                    ->orWhereRaw('UPPER(TRIM(email)) LIKE ?', ['%' . strtoupper(trim($searchValue)) . '%']);
            });
        }

        // Ejecutar la consulta y retornar los resultados
        return $query->get();
    }

    public function edit(string $id)
    {
        // Realizamos la consulta utilizando el Query Builder de Laravel
        $user = DB::table('administration.users')
            ->where('id', $id)
            ->first(); // Usamos first() para obtener un único registro

        // Retornamos el usuario o null si no se encuentra
        return $user ?? null;
    }

    public function validateEmail($userEmail, $userId)
    {
        // Limpiamos el email (convertimos a mayúsculas y eliminamos espacios)
        $cleanEmail = strtoupper(trim($userEmail));

        // Construimos la consulta base
        $query = DB::table('administration.users')
            ->whereRaw('UPPER(TRIM(email)) = ?', [$cleanEmail]);

        // Si $userId está definido, excluimos ese usuario de la búsqueda
        if (!empty($userId)) {
            $query->where('id', '<>', $userId);
        }

        // Ejecutamos la consulta
        $user = $query->first();

        // Retornamos true si el correo no existe (no se encontró un registro), false si ya existe
        return $user ? false : true;
    }

    // La funcion valida que la contraseña ingresada si existe
    public function validatePassword($idUser, $userPassword)
    {
        // Consultar la tabla 'users' dentro del esquema 'administration' usando DB::table()
        $user = DB::table('administration.users')
            ->where('id', $idUser)
            ->first(); // Traer el primer resultado

        // Verificar si el usuario existe y la contraseña coincide con la almacenada en la base de datos
        if ($user && Hash::check($userPassword, $user->password)) {
            return true; // Si la contraseña es correcta
        }

        return false; // Si no hay coincidencias
    }

    // La función obtiene el nombre de usuario
    public function getName($id)
    {
        // Usando DB::table() para obtener el primer registro y la clave en mayúsculas
        $result = DB::table('administration.users')
            ->where('id', $id)
            ->select(DB::raw('UPPER(name) AS nombre'))
            ->first();

        // Verificar si se encontró un resultado
        return $result ? $result->nombre : null;
    }
}
