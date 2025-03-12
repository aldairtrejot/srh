    // Obtener el token CSRF desde la metaetiqueta
    const token = $('meta[name="csrf-token"]').attr('content');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': token
        }
    });

    // Variables globales
    let iterator = 1;
    let emptyContent = false;
    let courseIdToDelete = null;

    $(document).ready(function () {
        searchInit();
        setValue();

        // Modal y eventos
        const modal = document.getElementById("deleteModal");
        const span = document.getElementsByClassName("close")[0];
        const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
        const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");

        span.onclick = () => (modal.style.display = "none");
        cancelDeleteBtn.onclick = () => (modal.style.display = "none");

        window.onclick = (event) => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };

        confirmDeleteBtn.onclick = () => {
            if (courseIdToDelete) deleteCourse(courseIdToDelete);
        };
    });

    // Función para inicializar la búsqueda
    function searchInit() {
        const searchValue = document.getElementById('searchValue').value.trim();
        const iteradorAux = (iterator * 5) - 5;

        $.ajax({
            url: `${URL_DEFAULT}/coursesauditoria/table`,
            type: 'POST',
            data: {
                iterator: iteradorAux,
                searchValue: searchValue,
                _token: token
            },
            success: (response) => renderTable(response),
            error: (xhr) => handleAjaxError(xhr)
    if (response.value && response.value.length > 0) {
        response.value.forEach((object) => {
            const finalUrl = `${URL_DEFAULT}/coursesauditoria/edit/${object.id_cat_auditoria}`;
            const rowHTML = `
                <tr>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-transparent dropdown-toggle-split icon-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: transparent;" data-toggle="tooltip" title="Menú">
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
                                <a class="dropdown-item" href="#" onclick="confirmDelete(${object.id_cat_auditoria})">
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
                    <td>${object.estatus ? 'ACTIVO' : 'INACTIVO'}</td>
                </tr>
            `;
            tbody.append(rowHTML);
        });
    }

    // Renderiza la tabla con los resultados
    function renderTable(response) {
        const tbody = $('#template-table tbody');
        tbody.empty();

        if (response.value && response.value.length > 0) {
            response.value.forEach((object) => {
                const finalUrl = `${URL_DEFAULT}/coursesauditoria/edit/${object.id_auditoria}`;
                const rowHTML = `
                    <tr>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-transparent dropdown-toggle-split icon-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: transparent;" data-toggle="tooltip" title="Menú">
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
                                    <a class="dropdown-item" href="#" onclick="confirmDelete(${object.id_auditoria})">
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

    // Manejo de errores en AJAX
    function handleAjaxError(xhr) {
        console.error('Error en la solicitud:', xhr.responseText);
        alert('Hubo un error al procesar la solicitud. Por favor, inténtalo de nuevo.');
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

    // Reinicia el iterador y realiza la búsqueda
    function searchValue() {
        iterator = 1;
        setValue();
        searchInit();
    }

    // Muestra el modal de confirmación de eliminación
    function confirmDelete(id) {
        courseIdToDelete = id;
        document.getElementById("deleteModal").style.display = "block";
    }

    // Elimina un curso
    function deleteCourse(id) {
        $.ajax({
            url: `${URL_DEFAULT}/coursesauditoria/delete/${id}`,
            type: 'DELETE',
            data: { _token: token },
            success: () => {
                alert('Auditoría eliminada exitosamente.');
                window.location.href = `${URL_DEFAULT}/coursesauditoria/list`;
            },
            error: (xhr) => handleAjaxError(xhr)
        });
    }
