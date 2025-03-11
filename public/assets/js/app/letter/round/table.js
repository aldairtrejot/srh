var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

var iterator = 1; // Se comienza el iterador en 1
var emptyContent = false;

$(document).ready(function () {
    searchInit();
    setValue();
});

function searchInit() {
    const searchValue = document.getElementById('searchValue').value;
    const iteradorAux = (iterator * 5) - 5;

    $.ajax({
        url: URL_DEFAULT.concat('/round/table'),
        type: 'POST',
        data: {
            iterator: iteradorAux,
            searchValue: searchValue,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            const tbody = $('#template-table tbody');
            tbody.empty(); // Limpiar la tabla

            if (response.value && response.value.length > 0) {
                response.value.forEach(function (object) {
                    const finalUrl = URL_DEFAULT.concat(`/round/edit/${object.id}`);
                    const finalCloud = URL_DEFAULT.concat(`/round/cloud/${object.id}`);
                    const urlReport = URL_DEFAULT.concat(`/round/generate-pdf/${object.id}`);

                    // Generar el HTML con template literals
                    const rowHTML = `
                        <tr>
                        <!--
                            <td>
                                <div class="dropdown">
                                    <button class="custom-button-x custom-button btn dropdown-toggle-split" type="button" id="dropdownMenuIconButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background:#10312b" data-toggle="tooltip" data-placement="top" title="Menú">
                                        <i style="color: white; font-size: 15px" class="fa fa-pencil"></i>
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
                                        </a>
                                         <a class="dropdown-item" href="${finalCloud}">
                                            <span style="background:#8a6f19" class="icon-container-template">
                                                <div style="text-align: center;">
                                                    <i class="fa fa-cloud item-icon-menu"></i>
                                                </div>
                                            </span>
                                            Cloud
                                        </a>
                                        <!--
                                         <a class="dropdown-item" href="${urlReport}">
                                            <span style="background:#707070" class="icon-container-template">
                                                <div style="text-align: center;">
                                                    <i class="fa fa-print item-icon-menu"></i>
                                                </div>
                                            </span>
                                            Reporte
                                        </a>
                                        <a class="dropdown-item custom-button-x" href="#" style="pointer-events: none; color: grey;">
                                            <span style="background:#003366" class="icon-container-template">
                                                <div style="text-align: center;">
                                                    <i class="fa fa-user item-icon-menu"></i>
                                                </div>
                                            </span>
                                            Usuario
                                        </a>
                                        <a class="dropdown-item custom-button-x" style="pointer-events: none; color: grey;">
                                            <span style="background:#6A1B3D" class="icon-container-template">
                                                <div style="text-align: center;">
                                                    <i class="fa fa-trash item-icon-menu"></i>
                                                </div>
                                            </span>
                                            Eliminar
                                        </a>
                                        -->
                                    </div>
                                </div>
                            </td>
                            -->

                            <td>
                                <div class="button-container" style="display: flex; gap: 2px;">
                                    <a href="${finalUrl}" style="background: #10312b; padding: 8px 12px;" class="custom-button custom-button-x" title="Modificar">
                                        <i style="color: white; font-size: 15px" class="fa fa-pencil"></i>
                                    </a>
                                    <a href="${finalCloud}" style="background: #8a6f19; padding: 8px 12px;" class="custom-button custom-button-x" title="Cloud">
                                        <i style="color: white; font-size: 15px" class="fa fa-cloud"></i>
                                    </a>
                                </div>
                            </td>
                            <td>${object.anio}</td>
                            <td>${object.num_turno_sistema}</td>
                            <td>${object.num_documento}</td>
                            <td style="font-size: 12px; width: 1100px; word-wrap: break-word; white-space: normal;">${object.asunto}</td>
                        </tr>
                    `;
                    tbody.append(rowHTML);
                });
                emptyContent = false;
                talldropdown(response.value.length, 1); // Scroll en dropw
            } else {
                tbody.html('<tr><td colspan="8" class="text-center">No se encontraron resultados</td></tr>');
                emptyContent = true;
                setValue();
            }
        },
    });
}

//Funcion para que al pulsar el boton se incremente uno
function paginatorMax1() {

    iterator = emptyContent ? iterator : iterator += 1;
    setValue();
    searchInit();
}

//Funcion para que al pulsar el boton se incrementen 5
function paginatorMax5() {
    iterator = emptyContent ? iterator : iterator += 5;
    setValue();
    searchInit();
}

//Funcion para que al pulsar el boton se disminuyan 5
function paginatorMin5() {
    let iteratorAux = iterator;
    iterator = (iteratorAux -= 5) > 0 ? (iterator -= 5) : 1;
    setValue();
    searchInit();
}

//Funcion para que al pulsar el boton se disminuyan 1
function paginatorMin1() {
    let iteratorAux = iterator;
    iterator = (iteratorAux -= 1) > 0 ? (iterator -= 1) : 1;
    setValue();
    searchInit();
}

// se establan los valores de los lavel 
function setValue() {

    let iteratorAux = iterator;
    document.getElementById("is_iterator").innerHTML = iteratorAux;
    document.getElementById("is_iteratorMin").innerHTML = iteratorAux -= 1;
    document.getElementById("is_iteratorMax").innerHTML = iteratorAux += 2;
}

//Al momento de escribir en buscador resetar variable a 1
function searchValue() {
    iterator = 1;
    setValue();
    searchInit();
}