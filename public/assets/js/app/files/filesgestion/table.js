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
let gestionIdToDelete = null;

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
        if (gestionIdToDelete) deleteGestion(gestionIdToDelete);
    };
});

// Inicializa búsqueda con AJAX
function searchInit() {
    const searchValue = document.getElementById('searchValue') ? document.getElementById('searchValue').value.trim() : '';
    const page = iterator;

    $.ajax({
        url: `${URL_DEFAULT}/filesgestion/table`,
        type: 'POST',
        data: {
            page: page,
            search: searchValue,
            _token: token
        },
        success: function (response) {
            const tbody = $('#template-table tbody');
            tbody.empty();

            if (response.data && response.data.length > 0) {
                response.data.forEach(function (object) {
                    const editUrl = `${URL_DEFAULT}/filesgestion/edit/${object.id_tbl_gestion_documentos || ''}`;
                    const rowHTML = `
                        <tr>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-transparent dropdown-toggle-split icon-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: transparent;" data-toggle="tooltip" data-placement="top" title="Menu">
                                        <i class="fas fa-ellipsis-h" style="color: #9F2241; font-size: 2rem;"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <h6 class="dropdown-header">Acciones</h6>
                                        <a class="dropdown-item" href="${editUrl}">
                                            <span style="background:#1D5B3B" class="icon-container-template">
                                                <div style="text-align: center;">
                                                    <i class="fa fa-pencil item-icon-menu"></i>
                                                </div>
                                            </span>
                                            Modificar
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="confirmDelete(${object.id_tbl_gestion_documentos})">
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
                            <td>${object.folio_documento || ''}</td>
                            <td>${object.fecha_registro || ''}</td>
                            <td>${object.observaciones || ''}</td>
                            <td>${object.id_ubicacion || ''}</td>
                            <td>${object.archivo || ''}</td>
                            <td>${object.es_fisico ? 'Sí' : 'No'}</td>
                            <td>${object.posicion || ''}</td>
                            <td>${object.fecha_ultima_ubicacion || ''}</td>
                            <td>${object.nombre_completo || ''}</td>
                        </tr>
                    `;
                    tbody.append(rowHTML);
                });

                emptyContent = false;
            } else {
                tbody.html('<tr><td colspan="10" class="text-center">No se encontraron resultados</td></tr>');
                emptyContent = true;
            }

        },
        error: function (xhr) {
            console.error('Error en la solicitud:', xhr.responseText);
            alert('Hubo un error al cargar los datos. Intenta de nuevo.');
        }
    });
}

// Muestra modal de confirmación
function confirmDelete(id) {
    gestionIdToDelete = id;
    document.getElementById("deleteModal").style.display = "block";
}

// Elimina un registro
function deleteGestion(id) {
    $.ajax({
        url: `${URL_DEFAULT}/filesgestion/delete/${id}`,
        type: 'DELETE',
        data: { _token: token },
        success: () => {
            alert('Expediente eliminado exitosamente.');
            document.getElementById("deleteModal").style.display = "none";
            searchInit();
        },
        error: (xhr) => {
            console.error('Error al eliminar:', xhr.responseText);
            alert('No se pudo eliminar el expediente. Intenta de nuevo.');
        }
    });
}

// Funciones de paginación
function paginatorMax1() {
    iterator++;
    setValue();
    searchInit();
}

function paginatorMax5() {
    iterator += 5;
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

// Reinicia el iterador y realiza la búsqueda desde la página 1
function searchValue() {
    iterator = 1;
    setValue();
    searchInit();
}

// Actualiza los valores del paginador en el DOM
function setValue() {
    const current = Math.max(iterator, 1);  // Realmente siempre empieza en 1
    const previous = current - 1;           // ← Permitimos que sea 0
    const next = current + 1;

    const currentEl = document.getElementById("is_iterator");
    const minEl = document.getElementById("is_iteratorMin");
    const maxEl = document.getElementById("is_iteratorMax");

    if (currentEl) currentEl.innerText = current;
    if (minEl) minEl.innerText = previous;
    if (maxEl) maxEl.innerText = next;
}

