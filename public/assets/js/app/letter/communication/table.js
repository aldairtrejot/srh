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
        url: URL_DEFAULT.concat('/communication/table'),
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
                    const finalUrl = URL_DEFAULT.concat(`/communication/edit/${object.id}`);

                    // Generar el HTML con template literals
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
                                </div>
                            </div>
                        </td>
                        <td>${object.num_oficio}</td>
                        <td>${object.tema}</td>
                        <td>${object.area}</td>
                        <td>${object.fecha}</td>
                        <td class="button-column">
                            ${object.uuid_oficio == null ? `
                                <button  onclick="addFileOficio('${object.id}')" style="background:#003366" class="custom-button centered-button" title="Cargar">
                                        <i style="color: white; font-size: 15px" class="fas fa-upload"></i>
                                    </button>
                            ` : `
                                <div class="button-container">
                                    <button onclick="seeDocumentUid('${object.uuid_oficio}')" style="background: #10312b" class="custom-button" title="Ver">
                                        <i style="color: white; font-size: 15px" class="fa fa-eye"></i>
                                    </button>
                                    <button onclick="download('${object.uuid_oficio}')" class="custom-button" title="Descargar">
                                        <i style="color: white; font-size: 15px" class="fa fa-download"></i>
                                    </button>
                                    <button onclick="openModalOificio('${object.uuid_oficio}')" style="background: #6A1B3D" class="custom-button" title="Eliminar">
                                        <i style="color: white; font-size: 15px" class="fa fa-trash"></i>
                                    </button>
                                </div>
                            `}
                        </td>
                        <td class="button-column">
                            ${object.uuid_acuse == null ? `
                                <button onclick="addFileAcuse('${object.id}')" style="background:#003366" class="custom-button centered-button" title="Cargar">
                                        <i style="color: white; font-size: 15px" class="fas fa-upload"></i>
                                    </button>
                            ` : `
                                <div class="button-container">
                                    <button onclick="seeDocumentUid('${object.uuid_acuse}')" style="background: #10312b" class="custom-button" title="Ver">
                                        <i style="color: white; font-size: 15px" class="fa fa-eye"></i>
                                    </button>
                                    <button onclick="download('${object.uuid_acuse}')" class="custom-button" title="Descargar">
                                        <i style="color: white; font-size: 15px" class="fa fa-download"></i>
                                    </button>
                                    <button onclick="openModalAcuse('${object.uuid_acuse}')" style="background: #6A1B3D" class="custom-button" title="Descargar">
                                        <i style="color: white; font-size: 15px" class="fa fa-trash"></i>
                                    </button>
                                </div>
                            `}
                        </td>
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