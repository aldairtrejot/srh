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

    const estatusColors = {
        "INACTIVO": "#660000", // Rojo
        "ACTIVO": "#26874A",   // Verde
    };

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
                    const finalUrl = `${URL_DEFAULT}/area/edit/${object.id}`;
                    const estatusTexto = object.estatus ? 'ACTIVO' : 'INACTIVO';
                    const estatusColor = estatusColors[estatusTexto] || '#999';

                    const rowHTML = `
                        <tr>
                            <td>
                                <div class="button-container" style="display: flex; gap: 2px;">
                                    <a href="${finalUrl}" style="background: #10312b; padding: 8px 12px;" class="custom-button custom-button-x" title="Modificar">
                                        <i style="color: white; font-size: 15px" class="fa fa-pencil"></i>
                                    </a>
                                </div>
                            </td>
                            <td>${object.descripcion}</td>
                            <td>${object.clave}</td>
                            <td>
                                <label style="background:${estatusColor}; color:white; padding: 4px 8px; border-radius: 6px;" class="badge">
                                    ${estatusTexto}
                                </label>
                            </td>
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


