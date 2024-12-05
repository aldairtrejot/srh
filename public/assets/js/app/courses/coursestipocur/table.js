var iterator = 1; // Se comienza el iterador en 1
var emptyContent = false;

$(document).ready(function () {
    searchInit();
    setValue();
});

function searchInit() {
    const searchValue = document.getElementById('searchValue').value;
    const iteradorAux = (iterator * 5) - 5;

    $.get('/srh/public/courses/table', {
        iterator: iteradorAux,
        searchValue: searchValue
    }, function (response) {

        console.log('success');
        console.log(response);


        const tbody = $('#template-table tbody');
        tbody.empty(); // Limpiar la tabla

        if (response.value && response.value.length > 0) {
            response.value.forEach(function (object) {
                const finalUrl = `/srh/public/courses/edit/${object.id}`;

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
                                    <button class="dropdown-item" onclick="showModalTempleta(${object.id})">
                                        <span style="background:#707070" class="icon-container-template">
                                            <div style="text-align: center;">
                                                <i class="fa fa-unlock item-icon-menu"></i>
                                            </div>
                                        </span>
                                        Cloud
                                    </button>
                                    <a class="dropdown-item" href="#">
                                        <span style="background:#003366" class="icon-container-template">
                                            <div style="text-align: center;">
                                                <i class="fa fa-user item-icon-menu"></i>
                                            </div>
                                        </span>
                                        Usuario
                                    </a>
                                    <a class="dropdown-item" href="#">
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
                        <td>${object.id_tipocursos}</td>
                        <td>${object.descripcion}</td>
                        <td>${object.estatus}</td>
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