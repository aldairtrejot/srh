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
        uploadFileOficio();
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
                    updateTableRow(response.data); // Actualizar la fila de la tabla
                } else {
                    notyfEM.error(response.messages);
                }
                searchInit(); // Ejecución de tabla para actualización de cambios

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
// Modificación en uploadFileOficio para enviar correctamente el ID y actualizar la tabla
function uploadFileOficio() {
    let fileInput = $('.file-input-oficio')[0];
    let file = fileInput.files[0]; // Obtener el primer archivo seleccionado
    let id_tbl_auditoria_cursos = $('#id_tbl_auditoria_cursos').val(); // Obtener el ID del curso

    if (!id_tbl_auditoria_cursos) {
        notyfEM.error("Error: No se encontró el ID del curso.");
        return;
    }

    if (file) {
        showSpinner(); // Mostrar spinner
        let data = new FormData();
        data.append('file', file);
        data.append('id', id_tbl_auditoria_cursos); // Asegurar que el ID se envíe correctamente

        $.ajax({
            url: URL_DEFAULT.concat("/auditoria/upload"),
            type: 'POST',
            data: data,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': token }, // Token CSRF para seguridad
            success: function (response) {
                hideSpinner(); // Ocultar spinner
                if (response.status) {
                    notyfEM.success("Archivo subido correctamente");

                    // Actualizar la tabla en la vista con el nuevo UUID
                    updateTableRow(id_tbl_auditoria_cursos, response.uuid);
                } else {
                    notyfEM.error(response.message);
                }

                searchInit(); // Refrescar tabla
                $('.file-input-oficio').val(''); // Limpiar input file
            },
            error: function (xhr, status, error) {
                console.error('Error al subir archivo:', error);
                notyfEM.error("Error al subir archivo. Intente nuevamente.");
                hideSpinner();
            }
        });
    } else {
        notyfEM.error("Seleccione un archivo antes de subir.");
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
                    rows += '<tr>' +
                            '<td>' + item.descripcion + '</td>' +
                            '<td>' +
                                '<div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">' +
                                    '<input type="checkbox" id="estatus" name="estatus" class="toggle-switch" ' + (item.aplica ? 'checked' : '') + '>' +
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



