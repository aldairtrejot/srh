// Obtener el token CSRF desde la metaetiqueta
const token = $('meta[name="csrf-token"]').attr('content');

// Configuración global para solicitudes AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': token
    }
});

// Variables globales
let iterator = 1; // Paginación actual
let emptyContent = false; // Indica si la tabla está vacía
let courseIdToDelete = null; // ID del curso a eliminar

// Inicializar cuando el documento esté listo
$(document).ready(function () {
    searchInit(); // Inicializar búsqueda
    setValue(); // Configurar valores iniciales de la paginación

    // Configuración del modal
    const modal = document.getElementById("deleteModal");
    const span = document.getElementsByClassName("close")[0];
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");

    span.onclick = () => (modal.style.display = "none");
    cancelDeleteBtn.onclick = () => (modal.style.display = "none");

    // Cierra el modal si se hace clic fuera de él
    window.onclick = (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };

    // Confirmar eliminación
    confirmDeleteBtn.onclick = () => {
        if (courseIdToDelete) deleteCourse(courseIdToDelete);
    };
});

// Inicializar búsqueda
function searchInit() {
    const searchValue = document.getElementById('searchValue').value.trim();
    const iteradorAux = Math.max((iterator - 1) * 5, 0); // Asegura que sea >= 0

    $.ajax({
        url: `${URL_DEFAULT}/coursesmodalidad/table`,
        type: 'POST',
        data: {
            iterator: iteradorAux,
            searchValue: searchValue || '', // Si está vacío, envía cadena vacía
            _token: token
        },
        success: function (response) {
            const tbody = $('#template-table tbody');
            tbody.empty();  // Limpiar la tabla antes de agregar los nuevos resultados

            if (response.value && response.value.length > 0) {
                response.value.forEach(function (object) {
                    const finalUrl = `/srh/public/coursesmodalidad/edit/${object.id_cat_modalidad}`;

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
                                       <!-- Aquí se agrega la opción para eliminar -->
                                        <a class="dropdown-item" href="#" onclick="confirmDelete(${object.id_cat_modalidad})">
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
                console.error('Respuesta inválida:', response);
                alert('Error en la respuesta del servidor.');
            }
        },
        error: handleAjaxError
    });
}

// Renderizar la tabla con los resultados
function renderTable(response) {
    const tbody = $('#template-table tbody');
    tbody.empty();

    if (response.value && Array.isArray(response.value) && response.value.length > 0) {
        response.value.forEach((object) => {
            const finalUrl = `${URL_DEFAULT}/coursesmodalidad/edit/${object.id_modalidad}`;
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
                                <a class="dropdown-item" href="#" onclick="confirmDelete(${object.id_modalidad})">
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
                    <td>${object.descripcion || '-'}</td>
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
}

// Manejo de errores en AJAX
function handleAjaxError(xhr) {
    console.error('Error en la solicitud:', xhr.status, xhr.responseText);
    alert(`Error ${xhr.status}: ${xhr.statusText}`);
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

// Reiniciar búsqueda
function searchValue() {
    iterator = 1;
    setValue();
    searchInit();
}

// Actualizar valores de la paginación en el DOM
function setValue() {
    document.getElementById("is_iterator").innerText = iterator;
    document.getElementById("is_iteratorMin").innerText = Math.max(iterator - 1, 1);
    document.getElementById("is_iteratorMax").innerText = iterator + 2;
}

// Mostrar modal para confirmar eliminación
function confirmDelete(id) {
    courseIdToDelete = id;
    document.getElementById("deleteModal").style.display = "block";
}

// Eliminar un curso
function deleteCourse(id) {
    $.ajax({
        url: `${URL_DEFAULT}/coursesmodalidad/delete/${id}`,
        type: 'DELETE',
        data: { _token: token },
        success: () => {
            alert('Modalidad eliminada exitosamente.');
            window.location.href = `${URL_DEFAULT}/coursesmodalidad/list`;
        },
        error: handleAjaxError
    });
}
