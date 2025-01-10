let isRequestInProgress = false;

function searchInit() {
    if (isRequestInProgress) return;
    isRequestInProgress = true;

    const searchValue = $('#searchValue').val();
    const iteradorAux = (iterator * 5) - 5;

    $.ajax({
        url: `${URL_DEFAULT}/tableinstructor/table`,
        type: 'POST',
        data: {
            iterator: iteradorAux,
            searchValue: searchValue,
            _token: token,
        },
        success: function (response) {
            const tbody = $('#template-table tbody');
            tbody.empty();
            if (response.data && response.data.length > 0) {
                response.data.forEach((object) => {
                    const editUrl = `${URL_DEFAULT}/tableinstructor/edit/${object.id_instructor}`;
                    const cloudUrl = `${URL_DEFAULT}/tableinstructor/cloud/${object.id_instructor}`;
                    const rowHTML = `
                        <tr>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-transparent dropdown-toggle-split icon-btn" type="button" id="dropdownMenuIconButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h" style="color: #9F2241; font-size: 2rem;"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuIconButton1">
                                        <h6 class="dropdown-header">Acciones</h6>
                                        <a class="dropdown-item" href="${editUrl}" title="Modificar Instructor">
                                            <span style="background:#1D5B3B" class="icon-container-template">
                                                <div style="text-align: center;">
                                                    <i class="fa fa-pencil item-icon-menu"></i>
                                                </div>
                                            </span>
                                            Modificar
                                        </a>
                                        <a class="dropdown-item" href="${cloudUrl}" title="Administrar datos en la nube">
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
                            <td>${object.id_instructor || 'Sin ID'}</td>
                            <td>${object.nombre || 'Sin nombre'}</td>
                            <td>${object.uuid_cv || 'Sin CV'}</td>
                            <td>${object.uuid_constancia || 'Sin constancia'}</td>
                            <td>${object.estatus_apto ? 'Apto' : 'No Apto'}</td>
                        </tr>
                    `;
                    tbody.append(rowHTML);
                });
            } else {
                tbody.html('<tr><td colspan="6" class="text-center">No se encontraron resultados</td></tr>');
            }
        },
        error: function (xhr, status, error) {
            console.error(`Error al cargar la tabla: ${error}`);
            console.error(`Detalles: ${xhr.responseText}`);
        },
        complete: function () {
            isRequestInProgress = false;
        }
    });
}
