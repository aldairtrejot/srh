const token = $('meta[name="csrf-token"]').attr('content');
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token } });

let iterator = 1;
let emptyContent = false;
let selectedIdToDelete = null;

$(document).ready(function () {
    searchInit();
    setValue();
});

function searchInit() {
    const searchValue = $('#searchValue').val().trim();
    const iteradorAux = (iterator * 5) - 5;

    $.ajax({
        url: `${URL_DEFAULT}/returned/table`,
        type: 'POST',
        data: {
            iterator: iteradorAux,
            searchValue: searchValue,
            _token: token
        },
        success: (response) => renderTable(response),
        error: (xhr) => console.error(xhr)
    });
}

function renderTable(response) {
    const tbody = $('#template-table tbody');
    tbody.empty();

    if (response.value && response.value.length > 0) {
        response.value.forEach((item) => {
            const urlEdit = `${URL_DEFAULT}/returned/edit/${item.id}`;
            const rowHTML = `
                <tr>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-transparent dropdown-toggle-split icon-btn" type="button" id="dropdownMenuIconButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: transparent;" data-toggle="tooltip" data-placement="top" title="Menú">
                                <i class="fas fa-ellipsis-h" style="color: #9F2241; font-size: 2rem;"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuIconButton1">
                                <h6 class="dropdown-header">Acciones</h6>
                                <a class="dropdown-item" href="${urlEdit}">
                                    <span style="background:#1D5B3B" class="icon-container-template">
                                        <div style="text-align: center;">
                                            <i class="fa fa-pencil item-icon-menu"></i>
                                        </div>
                                    </span>
                                    Modificar
                                </a>
                                <a class="dropdown-item" href="#" onclick="confirmDelete(${item.id})">
                                    <span style="background:#6A1B3D" class="icon-container-template">
                                        <div style="text-align: center;">
                                            <i class="fa fa-trash item-icon-menu"></i>
                                        </div>
                                    </span>
                                    Eliminar
                                </a>
                                <a class="dropdown-item" href="#" onclick="mostrarSubareas(${item.id})">
                                    <span style="background:#34495E" class="icon-container-template">
                                        <div style="text-align: center;">
                                            <i class="fa fa-sitemap item-icon-menu"></i>
                                        </div>
                                    </span>
                                    Subárea
                                </a>
                            </div>
                        </div>
                    </td>
                    <td>${item.clave}</td>
                    <td>${item.descripcion}</td>
                    <td>${item.estatus}</td>
                </tr>`;
            tbody.append(rowHTML);
        });
        emptyContent = false;
    } else {
        tbody.html('<tr><td colspan="4" class="text-center">No se encontraron resultados</td></tr>');
        emptyContent = true;
    }
}

function confirmDelete(id) {
    selectedIdToDelete = id;
    $('#deleteModal').show();
}

function deleteRecord() {
    if (!selectedIdToDelete) return;

    $.ajax({
        url: `${URL_DEFAULT}/returned/delete/${selectedIdToDelete}`,
        type: 'DELETE',
        data: { _token: token },
        success: () => {
            alert('Área eliminada correctamente.');
            window.location.href = `${URL_DEFAULT}/returned/list`;
        },
        error: (xhr) => console.error(xhr)
    });
}

function mostrarSubareas(idArea) {
    $.ajax({
        url: `${URL_DEFAULT}/returned/subareas/${idArea}`,
        type: 'GET',
        success: (response) => {
            if (response.length === 0) {
                Swal.fire('Sin resultados', 'No se encontraron subáreas para esta área.', 'info');
            } else {
         const htmlList = response.map(sub => `<li><strong>${sub.descripcion}</strong></li>`).join('');
                Swal.fire({
                    title: 'Subáreas',
                    html: `<ul style="text-align: left;">${htmlList}</ul>`,
                    confirmButtonText: 'Cerrar'
                });
            }
        },
        error: () => {
            Swal.fire('Error', 'No se pudo obtener la información de subáreas.', 'error');
        }
    });
}

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
function setValue() {
    $("#is_iterator").text(iterator);
    $("#is_iteratorMin").text(Math.max(iterator - 1, 1));
    $("#is_iteratorMax").text(iterator + 2);
}
