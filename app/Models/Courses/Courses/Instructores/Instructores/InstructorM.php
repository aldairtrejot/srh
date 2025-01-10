<?php

namespace App\Models\Courses\Courses\Instructores\Instructores;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InstructorM extends Model
{
    protected $table = 'capacitacion.tbl_instructores'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_instructor'; // Clave primaria
    public $timestamps = false; // Desactivar timestamps si no se usan en la tabla
    protected $fillable = [
        'id_instructor',
        'id_empleados',
        'uuid_constancia',
        'uuid_cv',
        'estatus_apto',
        'estatus_instructor',
        'id_usuario_sistema',
        'fecha_usuario',
    ];

    /**
     * Obtener un instructor por ID.
     *
     * @param string $id
     * @return object|null
     */
    public function edit(string $id)
    {
        return DB::table($this->table)
            ->where('id_instructor', $id)
            ->first();
    }

    /**
     * Obtener lista de instructores con paginación y búsqueda.
     *
     * @param int $iterator
     * @param string|null $searchValue
     * @return \Illuminate\Support\Collection
     */
    public function list($iterator = 0, $searchValue = null)
    {
        // Construcción de la consulta base
        $query = DB::table($this->table)
            ->select([
                "{$this->table}.id_instructor AS id",
                "{$this->table}.id_empleados",
                "{$this->table}.uuid_constancia",
                "{$this->table}.uuid_cv",
                DB::raw("CASE WHEN {$this->table}.estatus_apto = 1 THEN TRUE ELSE FALSE END AS estatus_apto"),
                "{$this->table}.estatus_instructor",
            ]);

        // Aplicar filtros de búsqueda si corresponde
        if (!empty($searchValue)) {
            $searchValue = strtoupper(trim($searchValue));
            $query->where(function ($subquery) use ($searchValue) {
                $subquery->whereRaw("UPPER(TRIM({$this->table}.id_empleados)) LIKE ?", ['%' . $searchValue . '%'])
                         ->orWhereRaw("UPPER(TRIM({$this->table}.uuid_constancia)) LIKE ?", ['%' . $searchValue . '%'])
                         ->orWhereRaw("UPPER(TRIM({$this->table}.uuid_cv)) LIKE ?", ['%' . $searchValue . '%']);
            });
        }

        // Aplicar orden y paginación
        $query->orderBy("{$this->table}.id_instructor", 'ASC')
              ->offset($iterator)
              ->limit(5);

        return $query->get();
    }
}
