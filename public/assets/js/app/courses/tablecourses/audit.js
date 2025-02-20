function searchInitaudit() {
    const searchValue = document.getElementById('searchValue').value; // Obtén el valor de búsqueda
    const iteradorAux = (iterator * 5) - 5;

    $.ajax({
        url: URL_DEFAULT.concat('auditoria/add/table'), 
        type: 'POST',
        data: {
            iterator: iteradorAux,  // Número de página para la paginación
            searchValue: searchValue, // Valor de búsqueda
            id_courses : $('#idtbl_cursos_audit').val(),
            _token: token,  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            const tbody = $('#table-responsive pt-3 tbody'); // Asegúrate de que seleccionas tbody
            tbody.empty();  // Limpiar la tabla antes de agregar los nuevos resultados

            if (response.value && response.value.length > 0) {
                response.value.forEach(function (object) {
                    const rowHTML = `
                        <tr>
                            <td>${object.id_cat_auditoria}</td>
                            <td>${object.estatus ? 'ACTIVO' : 'INACTIVO'}</td>
                            <td>${object.uuid_constancias}</td>
                        </tr>
                    `;
                    tbody.append(rowHTML);
                });
                emptyContent = false;
            } else {
                tbody.html('<tr><td colspan="3" class="text-center">No se encontraron resultados</td></tr>');
                emptyContent = true;
            }
        },
        error: function (xhr, status, error) {
            console.error('Error al buscar los datos de auditoría:', error);
            alert('Hubo un error al obtener los datos. Inténtalo de nuevo.');
        }
    });
}


// Función para que al pulsar el botón se incremente uno
function paginatorMax1() {
    iterator = emptyContent ? iterator : iterator += 1;
    setValue();
    searchInit();
}

// Función para que al pulsar el botón se incrementen 5
function paginatorMax5() {
    iterator = emptyContent ? iterator : iterator += 5;
    setValue();
    searchInit();
}

// Función para que al pulsar el botón se disminuyan 5
function paginatorMin5() {
    let iteratorAux = iterator;
    iterator = (iteratorAux -= 5) > 0 ? (iterator -= 5) : 1;
    setValue();
    searchInit();
}

// Función para que al pulsar el botón se disminuyan 1
function paginatorMin1() {
    let iteratorAux = iterator;
    iterator = (iteratorAux -= 1) > 0 ? (iterator -= 1) : 1;
    setValue();
    searchInit();
}

// Al escribir en el campo de búsqueda, se reinicia el iterador y se realiza la búsqueda
function searchValue() {
    iterator = 1;  // Reiniciar la paginación a la primera página
    setValue();  // Actualizar la visualización del número de página
    searchInit();  // Realizar la búsqueda
}

// Función para manejar la paginación y mostrar el número actual de la página
function setValue() {
    let iteratorAux = iterator;
    document.getElementById("is_iterator").innerHTML = iteratorAux;
    document.getElementById("is_iteratorMin").innerHTML = iteratorAux -= 1;
    document.getElementById("is_iteratorMax").innerHTML = iteratorAux += 2;
}

// Función para la confirmación de eliminación
function confirmDelete(id) {
    courseIdToDelete = id; // Almacenar el ID del curso a eliminar
    var modal = document.getElementById("deleteModal");
    modal.style.display = "block"; // Mostrar el modal
}

// Función para eliminar el curso
function deleteCourse(id) {
    $.ajax({
        url: `${URL_DEFAULT}/courses/delete/${id}`, // Verifica que esta ruta sea correcta
        type: 'DELETE',
        data: {
            _token: token  // Incluye el token CSRF
        },
        success: function(response) {
            window.location.href = '/srh/public/courses/list';  // Redirigir a la lista de cursos
        },
        error: function(xhr, status, error) {
            console.error('Error al eliminar el curso:', error);
            alert('Hubo un error al intentar eliminar el curso. Por favor, inténtalo de nuevo.');
        }
    });
}