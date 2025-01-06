// Importar la constante URL_DEFAULT desde el módulo correspondiente
import { URL_DEFAULT } from './url.js';

// Obtener el token CSRF
const token = $('meta[name="csrf-token"]').attr('content');

// Configurar AJAX con el token CSRF
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': token
    }
});

// Variables globales necesarias
let iterator = 1;  // Se comienza el iterador en 1
let emptyContent = false;
let courseIdToDelete = null; // Variable para almacenar el ID del curso a eliminar

// Función inicial para configurar la búsqueda y el paginador
function initialize() {
    // Configurar búsqueda inicial y el valor del paginador
    searchInit();
    setValue();

    // Configurar eventos del modal
    setupModalEvents();

    // Configurar eventos de paginación
    setupPaginationEvents();
}

// Configurar eventos del modal
function setupModalEvents() {
    const modal = document.getElementById("deleteModal");
    const span = document.getElementsByClassName("close")[0];
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");

    // Cerrar el modal al hacer clic en "x"
    span.onclick = function () {
        modal.style.display = "none";
    };

    // Cerrar el modal al hacer clic en cancelar
    cancelDeleteBtn.onclick = function () {
        modal.style.display = "none";
    };

    // Cerrar el modal al hacer clic fuera de él
    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };

    // Confirmar eliminación
    confirmDeleteBtn.onclick = function () {
        if (courseIdToDelete) {
            deleteCourse(courseIdToDelete);
        }
    };
}

// Configurar eventos de paginación
function setupPaginationEvents() {
    document.getElementById("paginatorMax1").addEventListener('click', paginatorMax1);
    document.getElementById("paginatorMax5").addEventListener('click', paginatorMax5);
    document.getElementById("paginatorMin1").addEventListener('click', paginatorMin1);
    document.getElementById("paginatorMin5").addEventListener('click', paginatorMin5);
}

// Función de búsqueda inicial con AJAX
function searchInit() {
    const searchValue = document.getElementById('searchValue').value; // Obtén el valor de búsqueda
    const iteradorAux = (iterator * 5) - 5;

    $.ajax({
        url: URL_DEFAULT + '/coursesauditoria/table',
        type: 'POST',
        data: {
            iterator: iteradorAux,
            searchValue: searchValue,
            _token: token,
        },
        success: function (response) {
            const tbody = $('#template-table tbody');
            tbody.empty();

            if (response.value && response.value.length > 0) {
                response.value.forEach(function (object) {
                    const finalUrl = URL_DEFAULT + `/coursesauditoria/edit/${object.id_auditoria}`;
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
                                        <a class="dropdown-item" href="#" onclick="confirmDelete(${object.id_auditoria})">
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
        }
    });
}

// Funciones para manejar la paginación
function paginatorMax1() {
    iterator = emptyContent ? iterator : iterator + 1;
    setValue();
    searchInit();
}

function paginatorMax5() {
    iterator = emptyContent ? iterator : iterator + 5;
    setValue();
    searchInit();
}

function paginatorMin1() {
    iterator = Math.max(iterator - 1, 1);
    setValue();
    searchInit();
}

function paginatorMin5() {
    iterator = Math.max(iterator - 5, 1);
    setValue();
    searchInit();
}

// Configurar la visualización del número de página
function setValue() {
    document.getElementById("is_iterator").innerHTML = iterator;
    document.getElementById("is_iteratorMin").innerHTML = iterator - 1;
    document.getElementById("is_iteratorMax").innerHTML = iterator + 1;
}

// Confirmar eliminación
function confirmDelete(id) {
    courseIdToDelete = id;
    document.getElementById("deleteModal").style.display = "block";
}

// Eliminar curso
function deleteCourse(id) {
    $.ajax({
        url: URL_DEFAULT + '/coursesauditoria/delete/' + id,
        type: 'DELETE',
        data: { _token: token },
        success: function () {
            window.location.href = URL_DEFAULT + '/coursesauditoria/list';
        },
        error: function () {
            alert('Hubo un error al intentar eliminar el curso. Por favor, inténtalo de nuevo.');
        }
    });
}

// Inicializar eventos y configuraciones al cargar el DOM
document.addEventListener('DOMContentLoaded', initialize);
