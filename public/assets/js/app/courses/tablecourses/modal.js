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






