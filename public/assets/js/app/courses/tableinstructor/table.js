// Obtener el token CSRF desde la metaetiqueta
const token = $('meta[name="csrf-token"]').attr('content');
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': token
    }
});

// Variables globales
var iterator = 1;  // Página actual (inicia en 1)
var emptyContent = false; // Controla si hay contenido en la tabla
var courseIdToDelete = null; // Almacena el ID del curso a eliminarID del curso a eliminar


$(document).ready(function () {
    searchInit(); // Carga inicial de la tabla
    setValue();   // Configura la paginación

    // Modal de confirmación de eliminación
    var modal = document.getElementById("deleteModal");
    var span = document.getElementsByClassName("close")[0];
    var confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    var cancelDeleteBtn = document.getElementById("cancelDeleteBtn");

    // Cerrar el modal al hacer clic en "x"
    span.onclick = function() {
        modal.style.display = "none";
    };

    // Cerrar el modal al hacer clic en "Cancelar"
    cancelDeleteBtn.onclick = function() {
        modal.style.display = "none";
    };

    // Cerrar el modal al hacer clic fuera de él
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };

    // Confirmar la eliminación
    confirmDeleteBtn.onclick = function() {
        if (courseIdToDelete) {
            deleteCourse(courseIdToDelete);
        }
    };
});



// **🔹 Función para inicializar la búsqueda con paginación**
function searchInit() {
    const searchValue = $('#searchValue').val(); // Usar jQuery para obtener el valor
    $.ajax({
        url: `${URL_DEFAULT}/tableinstructor/table`,
        type: 'POST',
        data: {
            iterator: iterator,
            searchValue: searchValue,
            _token: token
        },
        success: function(response) {
            const tbody = $('#template-table tbody');
            tbody.empty(); // Limpiar tabla

            if (response.data && response.data.length > 0) {
                response.data.forEach(function (object) {
                    const finalUrl = `${URL_DEFAULT}/tableinstructor/edit/${object.id_tbl_instructores}`;
                    const estatus = object.estatus_instructor && object.estatus_instructor.trim().toUpperCase() === "ACTIVO" ? "ACTIVO" : "INACTIVO";
                    const finalCloud = URL_DEFAULT.concat(`/tableinstructor/cloud/${object.id_tbl_instructores}`);
                    const urlReport = URL_DEFAULT.concat(`/tableinstructor/generate-pdf/constancias/${object.id_tbl_instructores}`);
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
                                        <a class="dropdown-item" href="${finalCloud}">
                                            <span style="background:#8a6f19" class="icon-container-template">
                                                <div style="text-align: center;">
                                                    <i class="fa fa-cloud item-icon-menu"></i>
                                                </div>
                                            </span>
                                            Cloud
                                        </a>
                                         <!-- Nuevo Botón Constancia -->
                                        <a class="dropdown-item" href="${urlReport}">
                                            <span style="background:#1E90FF" class="icon-container-template">
                                                <div style="text-align: center;">
                                                    <i class="fa fa-file item-icon-menu"></i>
                                                </div>
                                            </span>
                                            Constancia
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="confirmDelete(${object.id_tbl_instructores})">
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
                            <td>${object.curp || '-'}</td>
                            <td>${object.nombre_completo || '-'}</td>
                            <td>${estatus}</td>
                        </tr>
                    `;
                    tbody.append(rowHTML);
                });
            } else {
                tbody.html('<tr><td colspan="4" class="text-center">No se encontraron resultados</td></tr>');
            }
        },
        error: function(xhr) {
            console.error("Error en la búsqueda:", xhr);
        }
    });
}

// **🔹 Muestra el modal de confirmación de eliminación**
function confirmDelete(id) {
    courseIdToDelete = id;
    document.getElementById("deleteModal").style.display = "block";
}

// **🔹 Elimina un curso**
function deleteCourse(id) {
    console.log("🗑️ Intentando eliminar el instructor con ID:", id);

    $.ajax({
        url: `${URL_DEFAULT}/tableinstructor/delete/`, 
        type: 'POST', 
        data: { 
            id: id, 
            _token: token 
        },
        success: function(response) {
            console.log("✅ Respuesta del servidor:", response);
            if (response.status) {
                alert('✅ Instructor eliminado correctamente.');
                searchInit();
            } else {
                alert('❌ No se pudo eliminar el instructor.');
            }
        },
        error: function(xhr) {
            console.error("❌ Error en la eliminación:", xhr.responseText);
            alert("❌ Ocurrió un error al intentar eliminar. Revisa la consola y logs.");
        }
    });
}


// **🔹 Funciones para manejar la paginación**
function paginatorMax1() {
    iterator += 1;
    setValue();
    searchInit();
}

function paginatorMax5() {
    iterator += 5;
    setValue();
    searchInit();
}

function paginatorMin1() {
    iterator = Math.max(1, iterator - 1);
    setValue();
    searchInit();
}

function paginatorMin5() {
    iterator = Math.max(1, iterator - 5);
    setValue();
    searchInit();
}

// **🔹 Función para realizar la búsqueda al escribir en el campo de texto**
function searchValue() {
    iterator = 1; // Reiniciar la paginación
    setValue(); // Actualizar la paginación
    searchInit(); // Realizar la búsqueda
}

// **🔹 Función para manejar la paginación y mostrar el número actual de la página**
function setValue() {
    let iteratorAux = iterator;
    document.getElementById("is_iterator").innerHTML = iteratorAux;
    document.getElementById("is_iteratorMin").innerHTML = iteratorAux - 1;
    document.getElementById("is_iteratorMax").innerHTML = iteratorAux + 1;
}
