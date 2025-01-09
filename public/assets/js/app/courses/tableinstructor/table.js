var token = $('meta[name="csrf-token"]').attr('content'); // Token CSRF
var iterator = 1; // Iterador inicial
var emptyContent = false;

// Inicialización del documento
$(document).ready(function () {
    if (typeof URL_DEFAULT === 'undefined') {
        console.error("La variable URL_DEFAULT no está definida.");
        return;
    }
    searchInit(); // Inicializar la búsqueda
    setValue();   // Establecer valores iniciales
});

// Carga los datos en la tabla
function searchInit() {
    const searchValue = $('#searchValue').val();
    const iteradorAux = (iterator * 5) - 5;

    $.ajax({
        url: `${URL_DEFAULT}/tableinstructor/table`,
        type: 'POST',
        data: {
            iterator: iteradorAux,
            searchValue: searchValue,
            _token: token, // Token CSRF
        },
        success: function (response) {
            const tbody = $('#template-table tbody');
            tbody.empty(); // Limpia la tabla

            if (response.data && response.data.length > 0) {
                response.data.forEach((object) => {
                    const editUrl = `${URL_DEFAULT}/tableinstructor/edit/${object.id_instructor}`;
                    const cloudUrl = `${URL_DEFAULT}/tableinstructor/cloud/${object.id_instructor}`;

                    const rowHTML = `
                        <tr>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-transparent dropdown-toggle-split icon-btn" type="button" id="dropdownMenuIconButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Acciones de fila">
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
                            <td>${object.nombre || 'Sin nombre'}</td>
                            <td>${object.uuid_cv || 'Sin CV'}</td>
                            <td>${object.uuid_constancia || 'Sin constancia'}</td>
                            <td>${object.estatus_apto ? 'Apto' : 'No Apto'}</td>
                        </tr>
                    `;
                    tbody.append(rowHTML);
                });
                emptyContent = false;
            } else {
                tbody.html('<tr><td colspan="6" class="text-center">No se encontraron resultados</td></tr>');
                emptyContent = true;
            }
            setValue();
        },
        error: function (xhr, status, error) {
            console.error(`Error al cargar la tabla: ${error}`);
            alert("Hubo un problema al cargar los datos.");
        },
    });
}

// Funciones de paginación
function changeIterator(change) {
    iterator = Math.max(1, iterator + change);
    setValue();
    searchInit();
}

// Establece los valores del paginador
function setValue() {
    $("#is_iterator").text(iterator);
    $("#is_iteratorMin").text(Math.max(1, iterator - 1));
    $("#is_iteratorMax").text(iterator + 2);
}

// Reinicia el iterador al cambiar el valor de búsqueda
function searchValue() {
    iterator = 1;
    setValue();
    searchInit();
}
