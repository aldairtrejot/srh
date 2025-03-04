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
    $('#id_tbl_auditoria_cursos').val(id); // Se define el id oculto para su validación
    $('.file-input-oficio').click(); // Se abre el botón para agregar archivo
}
$('.file-input-oficio').on('change', function (event) {
    let file = event.target.files[0]; // Obtener el primer archivo seleccionado

    if (file) {
        if (file) {
            showSpinner();// Inicio de spinner
            let data = new FormData();// Crear el objeto FormData
            data.append('file', file);
            data.append('id', $('#id_oficio').val());
            $.ajax({
                url: URL_DEFAULT.concat("/auditoria/upload"),
                type: 'POST',
                data:
                    data, // Enviar directamente el FormData
                processData: false,  // No procesar los datos, jQuery no debe intentar convertir los datos en una cadena
                contentType: false,  // No establecer un Content-Type porque el navegador lo hará automáticamente
                headers: {
                    'X-CSRF-TOKEN': token  // Usar el token CSRF para proteger la solicitud
                },
                success: function (response) {
                    hideSpinner(); // Se oculta el spinner

                    if (response.status) { //Validacion si es que los cambios se han agregado correctamente
                        notyfEM.success("Doc. Oficio agregado correctamente.");
                    } else {
                        notyfEM.error(response.messages);
                    }
                    searchInit(); // Ejecucion de tabla para actualizacion de cambios

                    $('.file-input-oficio').val('');
                    $('#id_oficio').val('');
                },
            });
        }
    }
});

// Función para subir el archivo a Alfresco
function uploadFileOficio() {
    var formData = new FormData();
    formData.append('file', $('.file-input-oficio')[0].files[0]);
    formData.append('id_tbl_auditoria_cursos', $('#id_tbl_auditoria_cursos').val());
    formData.append('_token', token);

    $.ajax({
        url: URL_DEFAULT.concat('/auditoria/upload'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.status) {
                console.log('Archivo subido correctamente');
                updateTableRow(response.data);
            } else {
                console.error('Error al subir archivo: ', response.messages);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error al subir archivo: ', error);
        }
    });
}

// Función para actualizar la fila de la tabla después de subir el archivo
function updateTableRow(data) {
    var row = $('#template-tableaudit tbody').find('tr').filter(function () {
        return $(this).find('button').attr('onclick').includes(data.id);
    });

    row.find('.button-column').html(`
        <div class="button-container">
            <button onclick="seeDocumentUid('${data.uuid}')" style="background: #10312b" class="custom-button" title="Ver">
                <i style="color: white; font-size: 15px" class="fa fa-eye"></i>
            </button>
            <button onclick="download('${data.uuid}')" class="custom-button" title="Descargar">
                <i style="color: white; font-size: 15px" class="fa fa-download"></i>
            </button>
            <button onclick="openModalOificio('${data.uuid}')" style="background: #6A1B3D" class="custom-button" title="Eliminar">
                <i style="color: white; font-size: 15px" class="fa fa-trash"></i>
            </button>
        </div>
    `);
}

// Función para buscar la lista de auditorías
function searchInitaudit() {
    $.ajax({
        url: URL_DEFAULT.concat('/auditoria/list/courses'),
        type: 'POST',
        data: {
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            // Limpiar la tabla antes de agregar nuevos datos
            $('#template-tableaudit tbody').empty();

            // Iterar sobre los datos recibidos y agregarlos a la tabla
            response.data.forEach(function (item) {
                $('#template-tableaudit tbody').append(
                    '<tr>' +
                    '<td>' + item.descripcion + '</td>' +
                    '<td>' + (item.aplica ? 'Sí' : 'No') + '</td>' +
                    '<td><input type="file" name="constancia_' + item.id + '"></td>' +
                    '</tr>'
                );
            });

            console.log('Datos de auditoría cargados');
        },
        error: function (xhr, status, error) {
            console.error('Error al cargar datos de auditoría: ', error);
        }
    });
}






