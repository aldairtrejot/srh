// Obtener el token CSRF desde la metaetiqueta
const token = $('meta[name="csrf-token"]').attr('content');
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': token
    }
});

// Variables globales
let iterator = 1; // Página actual
let totalRecords = 0; // Total de registros
let recordsPerPage = 5; // Registros por página

$(document).ready(function () {
    searchInit();
    setPaginator();

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

// **🔹 Función para inicializar la búsqueda con paginación**
function searchInit() {
    const searchValue = document.getElementById('searchValue').value.trim();
    const offset = (iterator - 1) * recordsPerPage; // Calcular el desplazamiento (offset)

    $.ajax({
        url: `${URL_DEFAULT}/tableinstructor/table`,
        type: 'POST',
        data: {
            iterator: offset,
            searchValue: searchValue,
            _token: token
        },
        success: function (response) {
            renderTable(response.value);
            totalRecords = response.totalRecords || 0; // Obtener total de registros
            setPaginator();
        },
        error: function (xhr) {
            console.error('Error en la solicitud:', xhr.responseText);
            alert('Hubo un error al cargar los datos.');
        }
    });
}

// **🔹 Función para renderizar la tabla con los registros**
function renderTable(data) {
    const tbody = $('#template-table tbody');
    tbody.empty();

    if (data && data.length > 0) {
        data.forEach(function (object) {
            const finalUrl = `${URL_DEFAULT}/tableinstructor/edit/${object.id_tbl_instructores}`;
            const estatus = object.estatus_instructor && object.estatus_instructor.trim().toUpperCase() === "ACTIVO" ? "ACTIVO" : "INACTIVO";

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
                    <td>${object.curp || '-'}</td>
                    <td>${object.nombre_completo || '-'}</td>
                    <td>${estatus}</td>
                </tr>
            `;
            tbody.append(rowHTML);
        });
    } else {
        tbody.html('<tr><td colspan="4" class="text-center">No se encontraron resultados</td></tr>');
    }
}

// **🔹 Función para actualizar la paginación**
function setPaginator() {
    const totalPages = Math.ceil(totalRecords / recordsPerPage) || 1;

    document.getElementById("is_iterator").innerText = iterator;
    document.getElementById("is_iteratorMin").innerText = Math.max(iterator - 1, 1);
    document.getElementById("is_iteratorMax").innerText = totalPages;

    // Habilitar/Deshabilitar botones de paginación
    $("#paginatorMin1").prop("disabled", iterator === 1);
    $("#paginatorMin5").prop("disabled", iterator <= 5);
    $("#paginatorMax1").prop("disabled", iterator >= totalPages);
    $("#paginatorMax5").prop("disabled", iterator + 5 >= totalPages);
}

// **🔹 Funciones de paginación corregidas**
function paginatorMax1() {
    if (iterator < Math.ceil(totalRecords / recordsPerPage)) {
        iterator++;
        searchInit();
    }
}

function paginatorMax5() {
    if (iterator + 5 <= Math.ceil(totalRecords / recordsPerPage)) {
        iterator += 5;
        searchInit();
    }
}

function paginatorMin5() {
    iterator = Math.max(iterator - 5, 1);
    searchInit();
}

function paginatorMin1() {
    iterator = Math.max(iterator - 1, 1);
    searchInit();
}

// **🔹 Reinicia el iterador y realiza la búsqueda**
function searchValue() {
    iterator = 1;
    searchInit();
}

// **🔹 Función para eliminar un instructor**
function deleteCourse(id) {
    $.ajax({
        url: `${URL_DEFAULT}/tableinstructor/delete/${id}`,
        type: 'DELETE',
        data: { _token: token },
        success: () => {
            alert('Instructor eliminado exitosamente.');
            window.location.href = `${URL_DEFAULT}/tableinstructor/list`;
        },
        error: (xhr) => {
            console.error('Error en la eliminación:', xhr.responseText);
            alert('No se pudo eliminar el instructor.');
        }
    });
}
