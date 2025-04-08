
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
var instructorIdToDelete = null; 

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

function searchInit() {
    const searchValue = document.getElementById('searchValue').value.trim();
    const idUsuario = document.getElementById('idUsuario').value;

    $.ajax({
        url: `${URL_BASE}/assignedcourse/user-courses`,
        type: 'POST',
        data: {
            iterator: iterator,
            searchValue: searchValue,
            idUsuario: idUsuario,
            _token: token
        },
        success: function(response) {
            const tbody = $('#template-table tbody');
            tbody.empty();

            if (response.status && response.data.length > 0) {
                response.data.forEach(function (object) {
                    const urlReport = `${URL_BASE}/assignedcourse/generate-pdf/constancias/${object.id_usuarios ?? idUsuario}`;

                    const rowHTML = `
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
                            <td>${object.programa_proyecto ?? '-'}</td>
                            <td>${object.tipo_curso ?? '-'}</td>
                            <td>${object.horas ?? '-'}</td>
                            <td>${object.estatus ? 'ACTIVO' : 'INACTIVO'}</td>
                            <td>${object.fecha_inicio ?? '-'}</td>
                            <td>${object.fecha_fin ?? '-'}</td>
                            <td>${object.id_calificacion ?? '-'}</td>
                        </tr>
                    `;
                    tbody.append(rowHTML);
                });
            } else {
                tbody.html('<tr><td colspan="5" class="text-center"><strong>No se encontraron cursos asignados</td></tr>');
            }
        },
        error: function(xhr) {
            console.error("Error al obtener cursos:", xhr);
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
