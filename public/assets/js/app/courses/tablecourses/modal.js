var token = $('meta[name="csrf-token"]').attr('content'); // Token para las solicitudes
$(document).ready(function () {
    // Refresh no oficio
    $(window).click(function (event) {
        if ($(event.target).is('#modalBackdrop')) {
            $('#modalBackdrop').fadeOut(); // Ocultar la ventana modal
        }
    });

  // LA funcion activa el modal solicitante
function refreshOficio() {
    $('#modalBackdrop').fadeIn();//Iniciar ventana modal
}

    // Cerrar modal cuando se presiona el botón de cancelar
    $('#cancelBtn').click(function () {
        $('#modalBackdrop').fadeOut();
    });
});

// Función para abrir el modal de Auditoría
function refreshOficio() {
    $('#modalBackdrop').fadeIn(); // Mostrar la ventana modal
}

// Función para confirmar la auditoría dentro del modal
function confirmRefreshOficio() {
    console.log('Se está realizando la auditoría del oficio...');
    // Aquí puedes agregar la lógica para enviar datos o actualizar información
    $('#modalBackdrop').fadeOut(); // Cerrar el modal después de la confirmación
}
