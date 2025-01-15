var token = $('meta[name="csrf-token"]').attr('content'); // Token para las solicitudes
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': token
    }
});

// Variables globales
var iterator = 1;  // Página actual (inicia en 1)
var emptyContent = false; // Controla si hay contenido en la tabla
var courseIdToDelete = null; // Almacena el ID del curso a eliminar

$(document).ready(function () {
    searchInit(); // Carga inicial de la tabla
    setValue();   // Configura la paginación

    // Modal de confirmación de eliminación
    var modal = document.getElementById("deleteModal");
    var span = document.getElementsByClassName("close")[0];
    var confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    var cancelDeleteBtn = document.getElementById("cancelDeleteBtn");

    // Cerrar el modal al hacer clic en "x"
    span.onclick = function() {
        modal.style.display = "none";
    };

    // Cerrar el modal al hacer clic en "Cancelar"
    cancelDeleteBtn.onclick = function() {
        modal.style.display = "none";
    };

    // Cerrar el modal al hacer clic fuera de él
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };

    // Confirmar la eliminación
    confirmDeleteBtn.onclick = function() {
        if (courseIdToDelete) {
            deleteCourse(courseIdToDelete);
        }
    };
});

// Función para inicializar la búsqueda y cargar datos
function searchInit() {
    const searchValue = document.getElementById('searchValue').value; // Obtener el valor de búsqueda
    const iteradorAux = (iterator * 5) - 5; // Calcular el índice inicial para la paginación

    // Realizar la solicitud AJAX
    $.ajax({
        url: '/srh/public/tablecourses/table',
        type: 'POST',
        data: {
            iterator: iterator, // Número de página para la paginación
            searchValue: searchValue, // Valor de búsqueda
            _token: token,  // Token CSRF
        },
        success: function (response) {
            const tbody = $('#template-table tbody');
            tbody.empty(); // Limpiar la tabla antes de agregar nuevos resultados

            if (response.data && response.data.length > 0) {
                response.data.forEach(function (object) {
                    const finalUrl = `/srh/public/coursestipocur/edit/${object.id_tbl_cursos}`;

                    // Generar el HTML con template literals
                    const rowHTML = `
                        <tr>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-transparent dropdown-toggle-split icon-btn" type="button" id="dropdownMenuIconButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: transparent;" data-toggle="tooltip" data-placement="top" title="Menu">
                                        <i class="fas fa-ellipsis-h" style="color: #9F2241; font-size: 2rem;"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuIconButton1">
                                        <h6 class="dropdown-header">Acciones</h6>
                                        <a class="dropdown-item" href="${finalUrl}">
                                            <span style="background:#1D5B3B" class="icon-container-template">
                                                <div style="text-align: center;">
                                                    <i class="fa fa-pencil item-icon-menu"></i>
                                                </div>
                                            </span>
                                            Modificar
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="confirmDelete(${object.id_tbl_cursos})">
                                            <span style="background:#6A1B3D" class="icon-container-template">
                                                <div style="text-align: center;">
                                                    <i class="fa fa-trash item-icon-menu"></i>
                                                </div>
                                            </span>
                                            Eliminar
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>${object.nombre_curso}</td>
                            <td>${object.categoria_beneficio}</td>
                            <td>${object.categoria_tipo_curso}</td>
                            <td>${object.categoria_tipo_accion}</td>
                            <td>${object.categoria_programa_institucional}</td>
                            <td>${object.costo_total}</td>
                            <td>${object.fecha_inicio}</td>
                            <td>${object.fecha_fin}</td>
                            <td>${object.horas_curso}</td>
                            <td>${object.nombre_completo}</td>
                            <td>${object.estatus ? 'ACTIVO' : 'INACTIVO'}</td>
                        </tr>
                    `;
                    tbody.append(rowHTML);
                });
                emptyContent = false;
            } else {
                tbody.html('<tr><td colspan="12" class="text-center">No se encontraron resultados</td></tr>');
                emptyContent = true;
            }
        },
        error: function (xhr, status, error) {
            console.error('Error al cargar los datos:', error);
        }
    });
}

// Función para manejar la paginación y mostrar el número actual de la página
function setValue() {
    let iteratorAux = iterator;
    document.getElementById("is_iterator").innerHTML = iteratorAux;
    document.getElementById("is_iteratorMin").innerHTML = iteratorAux - 1;
    document.getElementById("is_iteratorMax").innerHTML = iteratorAux + 1;
}

// Función para confirmar la eliminación
function confirmDelete(id) {
    courseIdToDelete = id; // Almacenar el ID del curso a eliminar
    var modal = document.getElementById("deleteModal");
    modal.style.display = "block"; // Mostrar el modal
}

// Función para eliminar un curso
function deleteCourse(id) {
    $.ajax({
        url: `/srh/public/coursestipocur/delete/${id}`,
        type: 'DELETE',
        data: {
            _token: token // Incluir el token CSRF
        },
        success: function(response) {
            alert('Curso eliminado exitosamente.');
            searchInit(); // Actualizar la tabla
        },
        error: function(xhr, status, error) {
            console.error('Error al eliminar el curso:', error);
            alert('Hubo un error al intentar eliminar el curso. Intenta nuevamente.');
        }
    });
}

// Funciones para manejar la paginación
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

// Función para realizar la búsqueda al escribir en el campo de texto
function searchValue() {
    iterator = 1; // Reiniciar la paginación
    setValue(); // Actualizar la paginación
    searchInit(); // Realizar la búsqueda
}
