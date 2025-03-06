var token = $('meta[name="csrf-token"]').attr('content'); // Token para las solicitudes

$(document).ready(function () {
    // Cerrar modales al hacer clic fuera de ellos
    $(window).click(function (event) {
        if ($(event.target).is('#modalBackdrop')) {
            $('#modalBackdrop').fadeOut(); // Ocultar la ventana modal
        } else if ($(event.target).is('#modalSolicitante')) {
            $('#modalSolicitante').fadeOut(); // Ocultar la ventana modal
        }
    });

    // Cerrar modal cuando se presiona el botón de cancelar en el modalBackdrop
    $('#cancelBtn').click(function () {
        $('#modalBackdrop').fadeOut();
    });

    // Cerrar modal cuando se presiona el botón de cancelar en el modalSolicitante
    $('#cancelBtn_solicitante').click(function () {
        $('#modalSolicitante').fadeOut();
    });

    // Evento change para el input de archivo
    $('.file-input-oficio').change(function () {
        $('#id_oficio').fadeOut();
    });
});

// Función para abrir el modal de Auditoría
function refreshOficio(id_tbl_cursos) {
    console.log(id_tbl_cursos);
    $('#modalBackdrop').fadeIn();
    $('#idtbl_cursos_audit').val(id_tbl_cursos); // Mostrar la ventana modal con el ID
    console.log($('#idtbl_cursos_audit').val());
}

// Función para abrir el modal del Solicitante
function addSolicitante() {
    $('#modalSolicitante').fadeIn(); // Iniciar ventana modal
    $('#modalBackdrop').fadeOut();

    $.ajax({
        url: URL_DEFAULT.concat('/auditoria/add/courses'),
        type: 'POST',
        data: {
            id_courses: $('#idtbl_cursos_audit').val(),
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            console.log(response);
        },
        error: function (xhr, status, error) {
            console.error('Error al agregar solicitante: ', error);
        }
    });
}


// Función para confirmar la auditoría dentro del modal
function confirmRefreshOficio() {
    // Aquí puedes agregar la lógica para enviar datos o actualizar información
    $('#modalBackdrop').fadeOut(); // Cerrar el modal después de la confirmación

    $.ajax({
        url: URL_DEFAULT.concat('/auditoria/add/courses'),
        type: 'POST',
        data: {
            id_courses: $('#idtbl_cursos_audit').val(),
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            console.log('Auditoría confirmada');
            // Puedes agregar lógica para manejar la respuesta si es necesario
        },
        error: function (xhr, status, error) {
            console.error('Error al confirmar auditoría: ', error);
        }
    });

    $('#modalSolicitante').fadeIn(); // Iniciar ventana modal después de la confirmación
    searchInitaudit(); // Llamar a la función searchInitaudit si es necesario
}

// Función para guardar o validar contenido del Solicitante
function confirmSolicitante() {
    console.log('Confirmar solicitante');
    // Aquí puedes agregar la lógica de validación y envío para el solicitante
}

// Función que se activa al momento de presionar el botón de agregar
function addFileOficio(id) {
    console.log(id);
    $('#id_tbl_auditoria_cursos').val(id); // Se define el id oculto para su validación
    $('.file-input-oficio').click(); // Se abre el botón para agregar archivo
}
$('.file-input-oficio').on('change', function (event) {
    let file = event.target.files[0]; // Obtener el primer archivo seleccionado
console.log($('#id_tbl_auditoria_cursos').val())
    if (file) {
        showSpinner(); // Inicio de spinner
        let data = new FormData(); // Crear el objeto FormData
        data.append('file', file);
        data.append('id', $('#id_tbl_auditoria_cursos').val()); // Asegúrate de que el ID se envíe correctamente
        $.ajax({
            url: URL_DEFAULT.concat("/auditoria/upload"),
            type: 'POST',
            data: data, // Enviar directamente el FormData
            processData: false,  // No procesar los datos, jQuery no debe intentar convertir los datos en una cadena
            contentType: false,  // No establecer un Content-Type porque el navegador lo hará automáticamente
            headers: {
                'X-CSRF-TOKEN': token  // Usar el token CSRF para proteger la solicitud
            },
            success: function (response) {
                console.log(response);
                hideSpinner(); // Se oculta el spinner

                if (response.status) { // Validación si es que los cambios se han agregado correctamente
                    notyfEM.success("Se subio el archivo correctamente");
                   // updateTableRow(response.data); // Actualizar la fila de la tabla
                } else {
                    notyfEM.error(response.messages);
                }
                
                searchInitaudit(); // Ejecución de tabla para actualización de cambios

                $('.file-input-oficio').val('');
                $('#id_tbl_auditoria_cursos').val('');
            },
            error: function (xhr, status, error) {
                console.error('Error al subir el archivo: ', error);
                hideSpinner(); // Se oculta el spinner
            }
        });
    }
});

// Función para actualizar el estatus
function updateEstatus(id, estatus) {
    $.ajax({
        url: URL_DEFAULT.concat('/auditoria/update/estatus'),
        type: 'POST',
        data: {
            id: id,          // Enviar el ID del curso
            estatus: estatus, // El nuevo estatus (true o false)
            _token: token    // Token CSRF
        },
        success: function(response) {
            if (response.status) {
                console.log("Estatus actualizado correctamente");
                
            } else {
                console.error("Error al actualizar el estatus");
            }
            searchInitaudit(); // Llamar a searchInitaudit para refrescar la tabla
        },
        error: function(xhr, status, error) {
            console.error('Error al actualizar el estatus: ', error);
        }
    });
}
// Evento para cambiar el estatus cuando el interruptor se cambia
$('.toggle-switch').on('change', function() {
    // Obtener el ID de la fila (suponiendo que el ID está en un atributo data-uuid)
    let id = $(this).closest('tr').data('uuid');
    let estatus = $(this).prop('checked');  // Obtener el valor del interruptor (true o false)

    updateEstatus(id, estatus); // Llamar a la función para actualizar el estatus
});


function seeDocumentUid(uuid) {// ESTA FUNCION ES PARA PODER VISUALIZAR EL ARCHIVO QUE SE SUBIO A ALFRESCO
    $.ajax({
        url: URL_DEFAULT.concat("/auditoria/see"),
        type: "POST",
        data: {
            uid: uuid,
            _token: token // Token CSRF
        },
        xhrFields: {
            responseType: 'blob' // Para recibir archivos binarios (PDF, imágenes, etc.)
        },
        success: function (response, status, xhr) {
            let contentType = xhr.getResponseHeader("Content-Type");

            // Crear un objeto Blob con el contenido recibido
            let blob = new Blob([response], { type: contentType });

            // Crear una URL temporal para el archivo y abrirlo
            let url = window.URL.createObjectURL(blob);
            window.open(url, '_blank');
        },
        error: function (xhr, status, error) {
            console.error("Error al visualizar el documento:", error);
            alert("No se pudo abrir el documento.");
        }
    });
}
function download(uuid) {// ESTA FUNCION ES PARA DESCARGAR EL ARCHIVO QUE SE SUBIO EN ALFRESCO
    let form = document.createElement("form");
    form.setAttribute("method", "POST");
    form.setAttribute("action", URL_DEFAULT.concat("/auditoria/download"));

    let tokenInput = document.createElement("input");
    tokenInput.setAttribute("type", "hidden");
    tokenInput.setAttribute("name", "_token");
    tokenInput.setAttribute("value", token); // Token CSRF

    let uuidInput = document.createElement("input");
    uuidInput.setAttribute("type", "hidden");
    uuidInput.setAttribute("name", "uid");
    uuidInput.setAttribute("value", uuid);

    form.appendChild(tokenInput);
    form.appendChild(uuidInput);
    document.body.appendChild(form);
    form.submit();
}

function openModalOificio(uuid) {//ESTA FUNCION ES PARA ELIMINAR EL ARCHIVO QUE SE SUBIO EN ALFRESCO
    if (confirm("¿Estás seguro de que deseas eliminar este archivo? Esta acción no se puede deshacer.")) {
        fetch(URL_DEFAULT.concat('/auditoria/delete'), {
            method: "POST",  // Método POST para la eliminación
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token // Agregar el token CSRF
            },
            body: JSON.stringify({ uid: uuid })  // Enviar el UID en el cuerpo de la solicitud
        })
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                alert("Archivo eliminado correctamente.");
                searchInitaudit(); // Recargar la página para actualizar la lista
            } else {
                alert("Error al eliminar el archivo: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error en la solicitud:", error);
            alert("No se pudo eliminar el archivo. Inténtalo de nuevo.");
        });
    }
}


// Función para buscar la lista de auditorías
function searchInitaudit() {
    $.ajax({
        url: URL_DEFAULT.concat('/auditoria/list/courses'),
        type: 'POST',
        data: {
            id_tbl_cursos: $('#idtbl_cursos_audit').val(),
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            console.log(response);

            // Verificar si response.data.original es un array
            if (Array.isArray(response.data.original)) {
                // Limpiar la tabla antes de agregar nuevos datos
                let tableBody = $('#template-tableaudit tbody');
                tableBody.empty();

                // Construir el HTML de la tabla
                let rows = '';
                response.data.original.forEach(function (item) {
                    rows += '<tr data-uuid="${item.uuid}">' +
                            '<td style="font-size: 12px; width: 400px; word-wrap: break-word; white-space: normal;">' + item.descripcion + '</td>' +
                            '<td>' +
                               '<div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">' +
                                '<input type="checkbox" id="estatus" name="estatus" class="toggle-switch" checked>' +
                                '</div>' +

                            '</td>' +
                            '<td class="button-column">' +
                            (item.uuid == null ? ` 
                                <button onclick="addFileOficio('${item.id}')" style="background:#003366" class="custom-button centered-button" title="Cargar">
                                    <i style="color: white; font-size: 15px" class="fas fa-upload"></i>
                                </button>
                            ` : `
                                <div class="button-container">
                                    <button onclick="seeDocumentUid('${item.uuid}')" style="background: #10312b" class="custom-button" title="Ver">
                                        <i style="color: white; font-size: 15px" class="fa fa-eye"></i>
                                    </button>
                                    <button onclick="download('${item.uuid}')" class="custom-button" title="Descargar">
                                        <i style="color: white; font-size: 15px" class="fa fa-download"></i>
                                    </button>
                                    <button onclick="openModalOificio('${item.uuid}')" style="background: #6A1B3D" class="custom-button" title="Eliminar">
                                        <i style="color: white; font-size: 15px" class="fa fa-trash"></i>
                                    </button>
                                </div>
                            `) +
                            '</td>' +
                            '<td style="display:none;">' + item.id_cat_auditoria + '</td>' + // Campo oculto
                            '</tr>';
                });
               
                // Agregar las filas a la tabla
                tableBody.append(rows);

                console.log('Datos de auditoría cargados');
            } else {
                console.error('Formato de datos inesperado:', response.data);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error al cargar datos de auditoría: ', error);
        }
    });
}




