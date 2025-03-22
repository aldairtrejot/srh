const URL_BASE = window.location.origin + "/srh/public"; 

// Obtener el token CSRF desde la metaetiqueta
const token = $('meta[name="csrf-token"]').attr('content');
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': token
    }
});

// Variables globales
var iterator = 1;
var emptyContent = false; // Controla si hay contenido en la tabla

// ✅ Función para abrir el modal de confirmación
function confirmDelete(id) {
    instructorIdToDelete = id; // Guardar el ID
    $('#modalBackdrop').fadeIn(); // Mostrar modal de confirmación
}

$(document).ready(function () {
    searchInit();
    setValue();

    // Asegurar que los elementos existen antes de asignar eventos
    $(document).on('click', '#confirmBtn', function () {
        if (instructorIdToDelete) {
            deleteInstructor(instructorIdToDelete);
        }
    });

    $(document).on('click', '#cancelBtn', function () {
        $('#modalBackdrop').fadeOut();
    });

    $(document).on('click', '.close', function () {
        $('#modalBackdrop').fadeOut();
    });

    $(document).on('click', function (event) {
        if ($(event.target).attr('id') === 'modalBackdrop') {
            $('#modalBackdrop').fadeOut();
        }
    });
});

// 🔹 Función para inicializar la búsqueda con paginación
function searchInit() {
    const searchValue = document.getElementById('searchValue').value.trim();
    const idEmpleadoCursos = window.location.pathname.split('/').pop(); // Extrae el ID desde la URL
    const iteradorAux = (iterator * 5) - 5;
    
    $.ajax({
        url: `${URL_BASE}/assignedcourse/table`,
        type: 'POST',
        data: {
            searchValue: searchValue,
            iterator: iterator,
            idEmpleadoCursos: idEmpleadoCursos, // ✅ Ahora se envía el ID correcto
            _token: token
        },
        success: function(response) {
            const tbody = $('#template-table tbody');
            tbody.empty();
        
            if (response.data && response.data.length > 0) {
                response.data.forEach(function (object, index) {
                    if (object.id_empleado_cursos == idEmpleadoCursos) { 
                        const urlReport = `${URL_BASE}/assignedcourse/generate-pdf/constancias/${object.id_empleado_cursos}`;
                        const rowHTML =`
                            <tr>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-transparent dropdown-toggle-split icon-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: transparent;" data-toggle="tooltip" title="Menú">
                                            <i class="fas fa-ellipsis-h" style="color: #9F2241; font-size: 2rem;"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <h6 class="dropdown-header">Acciones</h6>
                                            
                                            <a class="dropdown-item" href="${urlReport}">
                                                <span style="background:#1E90FF" class="icon-container-template">
                                                    <div style="text-align: center;">
                                                        <i class="fa fa-file item-icon-menu"></i>
                                                    </div>
                                                </span>
                                                Constancia
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td>${object.programa_proyecto}</td>
                                <td>${object.tipo_curso}</td>
                                <td>${object.horas}</td>
                                <td>${object.estatus}</td>
                                <td>${object.fecha_inicio}</td>
                                <td>${object.fecha_fin}</td>
                                <td>${object.fecha_fin}</td>
                            </tr>
                        `;
                        tbody.append(rowHTML);
                    }
                });
                emptyContent = false;
            } else {
                tbody.html('<tr><td colspan="8" class="text-center"><strong>No se encontraron mas cursos asignados</strong></td></tr>');
                emptyContent = true;
            }
     
        },
        error: function(xhr) {
            console.error("Error en la búsqueda:", xhr);
        }
    });
}


// 🔹 Funciones para manejar la paginación
function paginatorMax1() {
    iterator += 1;
    setValue();
    searchInit();
}

function paginatorMax5() {
    iterator += 5;
    setValue();
    searchInit();
}

function paginatorMin1() {
    iterator = Math.max(1, iterator - 1);
    setValue();
    searchInit();
}

function paginatorMin5() {
    iterator = Math.max(1, iterator - 5);
    setValue();
    searchInit();
}

// 🔹 Función para realizar la búsqueda
function searchValue() {
    iterator = 1;
    setValue();
    searchInit();
}

// 🔹 Función para manejar la paginación y mostrar el número actual de la página
function setValue() {
    let iteratorAux = iterator;
    document.getElementById("is_iterator").innerHTML = iteratorAux;
    document.getElementById("is_iteratorMin").innerHTML = iteratorAux - 1;
    document.getElementById("is_iteratorMax").innerHTML = iteratorAux + 1;
}
