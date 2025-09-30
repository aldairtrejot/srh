var iterator = 1; // Se comienza el iterador en 1
var emptyContent = false;

$(document).ready(function () {
    searchInit();
    setValue();
});

function searchInit() {
    mostrarBarra();
    const startTime = Date.now();

    const searchValue = document.getElementById('searchValue').value;
    const iteradorAux = (iterator * 5) - 5;

    $.get(URL_DEFAULT.concat('/letter/table'), {
        iterator: iteradorAux,
        searchValue: searchValue
    }, function (response) {

        const tbody = $('#template-table tbody');
        tbody.empty();

        if (response.value && response.value.length > 0) {
            response.value.forEach(function (object) {
                const finalUrl   = URL_DEFAULT.concat(`/letter/edit/${object.id}`);
                const finalCloud = URL_DEFAULT.concat(`/letter/cloud/${object.id}`);
                const urlReport  = URL_DEFAULT.concat(`/letter/generate-pdf/correspondencia/${object.id}`);

                const estatusColors = {
                    "TURNADO": "#FFA82E",
                    "CANCELADO": "#660000",
                    "EN PROCESO": "#0077B6",
                    "CONCLUIDO": "#26874A",
                    "VENCIDO": "#FF0000",
                    "RECHAZADO": "#b30000",
                    "CONOCIMIENTO": "#6fc5f4ff",
                };
                const estatusColor = estatusColors[object.estatus] || "#6c757d";

                const rowHTML = `
                    <tr>
                        <td style="text-align: center;">
                            <div class="dropdown">
                                <button class="custom-button-x custom-button btn dropdown-toggle-split" type="button"
                                        id="dropdownMenuIconButton1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                        style="background:#10312b" data-toggle="tooltip" data-placement="top" title="Menú">
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
                                    <a class="dropdown-item" href="${finalCloud}">
                                        <span style="background:#8a6f19" class="icon-container-template">
                                            <div style="text-align: center;">
                                                <i class="fa fa-cloud item-icon-menu"></i>
                                            </div>
                                        </span>
                                        Cloud
                                    </a>
                                    <a class="dropdown-item" href="${urlReport}">
                                        <span style="background:#707070" class="icon-container-template">
                                            <div style="text-align: center;">
                                                <i class="fa fa-print item-icon-menu"></i>
                                            </div>
                                        </span>
                                        Reporte
                                    </a>
                                    <button class="dropdown-item" onclick="openCopy(${object.id}, '${object.folio_gestion}')">
                                        <span style="background:#691C32" class="icon-container-template">
                                            <div style="text-align: center;">
                                                <i class="fa fa-share-square item-icon-menu"></i>
                                            </div>
                                        </span>
                                        Copia
                                    </button>
                                    <button class="dropdown-item" onclick="opneEmail(${object.id}, '${object.folio_gestion}')">
                                        <span style="background:#462c95" class="icon-container-template">
                                            <div style="text-align: center;">
                                                <i class="fa fa-location-arrow item-icon-menu"></i>
                                            </div>
                                        </span>
                                        Email
                                    </button>
                                </div>
                            </div>
                        </td>
                        <td><label style="background:${estatusColor}; color:white" class="badge">${object.estatus}</label></td>
                        <td>${object.fecha_captura}</td>
                        <td>${object.folio_gestion}</td>
                        <td>${object.num_documento}</td>
                        <td style="font-size: 12px; width: 300px; word-wrap: break-word; white-space: normal;">${object.area || ''}</td>
                        <td style="font-size: 12px; width: 300px; word-wrap: break-word; white-space: normal;">${object.area_1 || ''}</td>
                        <td style="font-size: 12px; width: 300px; word-wrap: break-word; white-space: normal;">${object.area_2 || ''}</td>
                        <td style="font-size: 12px; width: 800px; word-wrap: break-word; white-space: normal;">${object.asunto}</td>
                    </tr>
                `;

                tbody.append(rowHTML);
            });
            emptyContent = false;
            talldropdown(response.value.length, 2);
        } else {
            tbody.html('<tr><td colspan="9" class="text-center">No se encontraron resultados</td></tr>');
            emptyContent = true;
            setValue();
        }

        const tiempoTranscurrido = Date.now() - startTime;
        const tiempoEspera = Math.max(0, 2000 - tiempoTranscurrido);

        setTimeout(ocultarBarra, tiempoEspera);

    });


}

function paginatorMax1() { iterator = emptyContent ? iterator : iterator += 1; setValue(); searchInit(); }
function paginatorMax5() { iterator = emptyContent ? iterator : iterator += 5; setValue(); searchInit(); }
function paginatorMin5() { let iteratorAux = iterator; iterator = (iteratorAux -= 5) > 0 ? (iterator -= 5) : 1; setValue(); searchInit(); }
function paginatorMin1() { let iteratorAux = iterator; iterator = (iteratorAux -= 1) > 0 ? (iterator -= 1) : 1; setValue(); searchInit(); }

function setValue() {
    let iteratorAux = iterator;
    document.getElementById("is_iterator").innerHTML = iteratorAux;
    document.getElementById("is_iteratorMin").innerHTML = iteratorAux -= 1;
    document.getElementById("is_iteratorMax").innerHTML = iteratorAux += 2;
}

function searchValue() {
    iterator = 1;
    setValue();
    searchInit();
}
