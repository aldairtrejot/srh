var token = $('meta[name="csrf-token"]').attr('content');
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': token
    }
});
var iterator = 1;
var emptyContent = false;
var courseIdToDelete = null;

$(document).ready(function () {
    searchInit();
    setValue();

    var modal = document.getElementById("deleteModal");
    var span = document.getElementsByClassName("close")[0];
    var confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    var cancelDeleteBtn = document.getElementById("cancelDeleteBtn");
    var successMessage = document.getElementById("successMessage");

    span.onclick = function() {
        modal.style.display = "none";
    }

    cancelDeleteBtn.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    confirmDeleteBtn.onclick = function() {
        if (courseIdToDelete) {
            deleteCourse(courseIdToDelete);
        }
    }
});

function searchInit() {
    const searchValue = document.getElementById('searchValue').value;
    const iteradorAux = (iterator * 5) - 5;

    $.ajax({
        url: `${URL_DEFAULT}/area/table`,
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
                    const finalUrl = `${URL_DEFAULT}/area/edit/${object.id_cat_area}`;

                    const rowHTML = `
                        <tr>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-transparent dropdown-toggle-split icon-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: transparent;" title="Menu">
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
                                        <a class="dropdown-item" href="#" onclick="confirmDelete(${object.id_cat_area})">
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
                            <td>${object.clave}</td>
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

function paginatorMax1() {
    iterator = emptyContent ? iterator : iterator += 1;
    setValue();
    searchInit();
}

function paginatorMax5() {
    iterator = emptyContent ? iterator : iterator += 5;
    setValue();
    searchInit();
}

function paginatorMin5() {
    let iteratorAux = iterator;
    iterator = (iteratorAux -= 5) > 0 ? (iterator -= 5) : 1;
    setValue();
    searchInit();
}

function paginatorMin1() {
    let iteratorAux = iterator;
    iterator = (iteratorAux -= 1) > 0 ? (iterator -= 1) : 1;
    setValue();
    searchInit();
}

function searchValue() {
    iterator = 1;
    setValue();
    searchInit();
}

function setValue() {
    let iteratorAux = iterator;
    document.getElementById("is_iterator").innerHTML = iteratorAux;
    document.getElementById("is_iteratorMin").innerHTML = iteratorAux -= 1;
    document.getElementById("is_iteratorMax").innerHTML = iteratorAux += 2;
}

function confirmDelete(id) {
    courseIdToDelete = id;
    var modal = document.getElementById("deleteModal");
    modal.style.display = "block";
}

function deleteCourse(id) {
    $.ajax({
        url: `${URL_DEFAULT}/area/delete/${id}`,
        type: 'DELETE',
        data: {
            _token: token
        },
        success: function(response) {
            window.location.href = '/srh/public/administration/administrationC/list';
        },
        error: function(xhr, status, error) {
            console.error('Error al eliminar el área:', error);
            alert('Hubo un error al intentar eliminar el área. Por favor, inténtalo de nuevo.');
        }
    });
}

