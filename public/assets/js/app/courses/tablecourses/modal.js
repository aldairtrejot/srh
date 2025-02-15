var token = $('meta[name="csrf-token"]').attr('content'); // Token para las solicitudes
$(document).ready(function () {
    // Refresh no oficio
    $(window).click(function (event) {
        if ($(event.target).is('#modalBackdrop')) {
            $('#modalBackdrop').fadeOut(); // Ocultar la ventana modal
        }
    });

    // Refresh add solicitante
    $(window).click(function (event) {
        if ($(event.target).is('#modalSolicitante')) {
            $('#modalSolicitante').fadeOut(); // Ocultar la ventana modal
        }
    });

    // Cerrar modal cuando se presiona el botón de cancelar
    $('#cancelBtn').click(function () {
        $('#modalBackdrop').fadeOut();
    });
});

// Cerrar modal refresh SOLICITANTE
$('#cancelBtn_solicitante').click(function () { //Se pulsa el boton de cancelar
    $('#modalSolicitante').fadeOut(); // Cerrar la ventana modal
});


// Función para abrir el modal de Auditoría
function refreshOficio(id_tbl_cursos) {
    console.log(id_tbl_cursos);
    $('#modalBackdrop').fadeIn();
    $('#idtbl_cursos_audit').val(id_tbl_cursos);// Mostrar la ventana modal
    console.log( $('#idtbl_cursos_audit').val());
}
// LA funcion activa el modal solicitante
function addSolicitante() {
    $('#modalSolicitante').fadeIn();//Iniciar ventana modal
}

// Función para confirmar la auditoría dentro del modal
function confirmRefreshOficio() {
    console.log( $('#idtbl_cursos_audit').val());
    // Aquí puedes agregar la lógica para enviar datos o actualizar información
    $('#modalBackdrop').fadeOut(); // Cerrar el modal después de la confirmación
    $('#modalSolicitante').fadeIn();//Iniciar ventana modal
}

// Guardar o validar contenido Solicitante
function confirmSolicitante() {
    console.log('confirm sol');
}

