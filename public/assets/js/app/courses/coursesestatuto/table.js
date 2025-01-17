const token = $('meta[name="csrf-token"]').attr('content'); // Token for form
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': token
    }
});

let iterator = 1; // Se comienza el iterador en 1
let emptyContent = false;
let courseIdToDelete = null; // Variable para almacenar el ID del curso a eliminar

$(document).ready(function () {
    searchInit(); // Inicializa la búsqueda cuando la página carga
    setValue(); // Inicializa paginador 

    // Obtener elementos del DOM
    const modal = document.getElementById("deleteModal");
    const span = document.getElementsByClassName("close")[0];
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");

    // Cuando el usuario hace clic en <span> (x), cerrar el modal
    span.onclick = () => modal.style.display = "none";

    // Cuando el usuario hace clic en el botón de cancelar, cerrar el modal
    cancelDeleteBtn.onclick = () => modal.style.display = "none";

    // Cuando el usuario hace clic fuera del modal, cerrarlo
    window.onclick = (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };

    // Cuando el usuario confirma la eliminación
    confirmDeleteBtn.onclick = () => {
        if (courseIdToDelete) {
            deleteCourse(courseIdToDelete);
        }
    };
});

// Función para inicializar la búsqueda
function searchInit() {
    const searchValue = document.getElementById('searchValue').value.trim(); // Obtén el valor de búsqueda
    const iteradorAux = (iterator * 5) - 5;

    $.ajax({
        url: `${URL_DEFAULT}/courses/table`,
        type: 'POST',
        data: {
            iterator: iteradorAux,
            searchValue,
            _token: token
        },
        success: function (response) {
            const tbody = $('#template-table tbody');
            tbody.empty(); // Limpiar la tabla antes de agregar los nuevos resultados

            if (response.value && response.value.length > 0) {
                response.value.forEach(function (object) {
                    const finalUrl = `/srh/public/coursesestatuto/edit/${object.id_cat_estatuto_organico}`;

                    // Generar el HTML con template literals
                    const rowHTML = `
                        <tr>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-transparent dropdown-toggle-split icon-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: transparent;" data-toggle="tooltip" title="Menú">
                                        <i class="fas fa-ellipsis-h" style="color: #9F2241; font-size: 2rem;"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <h6 class="dropdown-header">Acciones</h6>
                                        <a class="dropdown-item" href="${finalUrl}">
                                            <span style="background:#1D5B3B" class="icon-container-template">
                                                <div style="text-align: center;">
                                                    <i class="fa fa-pencil item-icon-menu"></i>
                                                </div>
                                            </span>
                                            Modificar
                                        </a>
                                       <!-- Aquí se agrega la opción para eliminar -->
                                        <a class="dropdown-item" href="#" onclick="confirmDelete(${object.id_cat_estatuto_organico})">
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
                            <td>${object.descripcion}</td>
                            <td>${object.estatus ? 'ACTIVO' : 'INACTIVO'}</td>
                        </tr>
                    `;
                    tbody.append(rowHTML);
                });
                emptyContent = false;
            } else {
                tbody.html('<tr><td colspan="8" class="text-center">No se encontraron resultados</td></tr>');
                emptyContent = true;
            }
        },
        error: function (xhr, status, error) {
            console.error('Error al cargar la tabla:', xhr.responseText);
        }
    });
}

// Funciones de paginación
function paginatorMax1() {
    if (!emptyContent) iterator += 1;
    setValue();
    searchInit();
}

function paginatorMax5() {
    if (!emptyContent) iterator += 5;
    setValue();
    searchInit();
}

function paginatorMin5() {
    iterator = Math.max(iterator - 5, 1);
    setValue();
    searchInit();
}

function paginatorMin1() {
    iterator = Math.max(iterator - 1, 1);
    setValue();
    searchInit();
}

// Reinicia el iterador y realiza la búsqueda
function searchValue() {
    iterator = 1;
    setValue();
    searchInit();
}

// Muestra el número actual de la página
function setValue() {
    document.getElementById("is_iterator").innerText = iterator;
    document.getElementById("is_iteratorMin").innerText = Math.max(iterator - 1, 1);
    document.getElementById("is_iteratorMax").innerText = iterator + 2;
}

// Confirma la eliminación
function confirmDelete(id) {
    courseIdToDelete = id;
    document.getElementById("deleteModal").style.display = "block";
}

// Elimina un curso
function deleteCourse(id) {
    $.ajax({
        url: `${URL_DEFAULT}/courses/delete/${id}`,
        type: 'DELETE',
        data: { _token: token },
        success: function () {
            alert('Curso eliminado exitosamente');
            window.location.href = `${URL_DEFAULT}/courses/list`;
        },
        error: function (xhr, status, error) {
            console.error('Error al eliminar el curso:', xhr.responseText);
            alert('Hubo un error al intentar eliminar el curso.');
        }
    });
}
