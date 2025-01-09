var token = $('meta[name="csrf-token"]').attr('content'); // Token para formularios

var iterator = 1; // Iterador inicial
var emptyContent = false;

$(document).ready(function () {
    searchInit(); // Inicializar búsqueda
    setValue();   // Establecer valores iniciales
});

// Inicializa la búsqueda y carga los datos en la tabla
function searchInit() {
    const searchValue = document.getElementById('searchValue').value;
    const iteradorAux = (iterator * 5) - 5;

    $.ajax({
        url: URL_DEFAULT.concat('/tableinstructor/table'),
        type: 'POST',
        data: {
            iterator: iteradorAux,
            searchValue: searchValue,
            _token: token // Token CSRF para autenticación
        },
        success: function (response) {
            const tbody = $('#template-table tbody');
            tbody.empty(); // Limpiar la tabla antes de agregar nuevos datos

            if (response.data && response.data.length > 0) {
                response.data.forEach(function (object) {
                    const editUrl = URL_DEFAULT.concat(`/tableinstructor/edit/${object.id_instructor}`);
                    const cloudUrl = URL_DEFAULT.concat(`/tableinstructor/cloud/${object.id_instructor}`);

                    // Generar el HTML de la fila
                    const rowHTML = `
                        <tr>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-transparent dropdown-toggle-split icon-btn" type="button" id="dropdownMenuIconButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: transparent;" data-toggle="tooltip" data-placement="top" title="Menú">
                                        <i class="fas fa-ellipsis-h" style="color: #9F2241; font-size: 2rem;"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuIconButton1">
                                        <h6 class="dropdown-header">Acciones</h6>
                                        <a class="dropdown-item" href="${editUrl}">
                                            <span style="background:#1D5B3B" class="icon-container-template">
                                                <div style="text-align: center;">
                                                    <i class="fa fa-pencil item-icon-menu"></i>
                                                </div>
                                            </span>
                                            Modificar
                                        </a>
                                        <a class="dropdown-item" href="${cloudUrl}">
                                            <span style="background:#8a6f19" class="icon-container-template">
                                                <div style="text-align: center;">
                                                    <i class="fa fa-cloud item-icon-menu"></i>
                                                </div>
                                            </span>
                                            Cloud
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>${object.id_instructor}</td>
                            <td>${object.nombre}</td>
                            <td>${object.uuid_cv || 'Sin CV'}</td>
                            <td>${object.uuid_constancia || 'Sin constancia'}</td>
                            <td>${object.estatus_apto ? 'Apto' : 'No Apto'}</td>
                        </tr>
                    `;
                    tbody.append(rowHTML);
                });
                emptyContent = false;
            } else {
                tbody.html('<tr><td colspan="8" class="text-center">No se encontraron resultados</td></tr>');
                emptyContent = true;
                setValue();
            }
        },
        error: function () {
            alert("Error al cargar la tabla.");
        }
    });
}

// Incrementa el iterador en 1
function paginatorMax1() {
    iterator = emptyContent ? iterator : iterator + 1;
    setValue();
    searchInit();
}

// Incrementa el iterador en 5
function paginatorMax5() {
    iterator = emptyContent ? iterator : iterator + 5;
    setValue();
    searchInit();
}

// Decrementa el iterador en 5
function paginatorMin5() {
    let iteratorAux = iterator;
    iterator = (iteratorAux - 5) > 0 ? iterator - 5 : 1;
    setValue();
    searchInit();
}

// Decrementa el iterador en 1
function paginatorMin1() {
    let iteratorAux = iterator;
    iterator = (iteratorAux - 1) > 0 ? iterator - 1 : 1;
    setValue();
    searchInit();
}

// Establece los valores de los labels del paginador
function setValue() {
    let iteratorAux = iterator;
    document.getElementById("is_iterator").innerHTML = iteratorAux;
    document.getElementById("is_iteratorMin").innerHTML = iteratorAux - 1;
    document.getElementById("is_iteratorMax").innerHTML = iteratorAux + 2;
}

// Resetea el iterador al cambiar el valor de búsqueda
function searchValue() {
    iterator = 1;
    setValue();
    searchInit();
}
