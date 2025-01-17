// Obtener el token CSRF desde la metaetiqueta
const token = $('meta[name="csrf-token"]').attr('content');
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': token
    }
});

// Variables globales
let iterator = 1;
let emptyContent = false;
let courseIdToDelete = null;

$(document).ready(function () {
    searchInit();
    setValue();

    // Modal y eventos
    const modal = document.getElementById("deleteModal");
    const span = document.getElementsByClassName("close")[0];
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");

    span.onclick = () => (modal.style.display = "none");
    cancelDeleteBtn.onclick = () => (modal.style.display = "none");

    window.onclick = (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };

    confirmDeleteBtn.onclick = () => {
        if (courseIdToDelete) deleteCourse(courseIdToDelete);
    };
});

// Función para inicializar la búsqueda
function searchInit() {
    const searchValue = document.getElementById('searchValue').value.trim();
    const iteradorAux = (iterator * 5) - 5;

    $.ajax({
        url: `${URL_DEFAULT}/tableinstructor/table`,
        type: 'POST',
        data: {
            iterator: iteradorAux,
            searchValue: searchValue,
            _token: token
        },
        success: (response) => renderTable(response),
        error: (xhr) => handleAjaxError(xhr)
    });
}

// Renderiza la tabla con los resultados
function renderTable(response) {
    const tbody = $('#template-table tbody');
    tbody.empty();

    if (response.value && response.value.length > 0) {
        response.value.forEach((object) => {
            const finalUrl = `${URL_DEFAULT}/tableinstructor/edit/{id}${object.id_tbl_instructores}`;
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
                                <a class="dropdown-item" href="#" onclick="confirmDelete(${object.id_tbl_instructores})">
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
                    <td>${object.curp || ' '}</td>
                    <td>${object.nombre_completo || ' '}</td>
                    <td>${object.estatus_instructor ? 'ACTIVO' : 'INACTIVO'}</td>
                </tr>
            `;
            tbody.append(rowHTML);
        });
        emptyContent = false;
    } else {
        tbody.html('<tr><td colspan="8" class="text-center">No se encontraron resultados</td></tr>');
        emptyContent = true;
    }
}

// Manejo de errores en AJAX
function handleAjaxError(xhr) {
    console.error('Error en la solicitud:', xhr.responseText);
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

// Actualiza el número de página en el DOM
function setValue() {
    document.getElementById("is_iterator").innerText = iterator;
    document.getElementById("is_iteratorMin").innerText = Math.max(iterator - 1, 1);
    document.getElementById("is_iteratorMax").innerText = iterator + 2;
}

// Muestra el modal de confirmación de eliminación
function confirmDelete(id) {
    courseIdToDelete = id;
    document.getElementById("deleteModal").style.display = "block";
}

// Elimina un curso
function deleteCourse(id) {
    $.ajax({
        url: `${URL_DEFAULT}/tableinstructor/delete/${id}`,
        type: 'DELETE',
        data: { _token: token },
        success: () => {
            alert('Instructor eliminado exitosamente.');
            window.location.href = `${URL_DEFAULT}/tableinstructor/list`;
        },
        error: (xhr) => handleAjaxError(xhr)
    });
}
