
//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

// Carga de formulario inicial
$(document).ready(function () {

    // Refresh no oficio
    $(window).click(function (event) {
        if ($(event.target).is('#modalBackdrop')) {
            $('#modalBackdrop').fadeOut(); // Ocultar la ventana modal
        }
    });

});

// ACTIVACION DE MODAL
// LA funcion activa el modal solicitante
function refresNota() {
    $('#modalBackdrop').fadeIn();//Iniciar ventana modal
}


// OCULTA MODAL
// Cerrar modal refresh no oficio
$('#cancelBtn').click(function () { //Se pulsa el boton de cancelar
    $('#update_letter').val(0); // Se establecen valores en 0 para que no se actualize el no Correspondencia
    $('#modalBackdrop').fadeOut(); // Cerrar la ventana modal
    $('#myForm').submit();
});


// GUARDAR CONTENIDO
// Guardar o validar contenido Solicitante
function confirmRefreshOficio() {
    $('#update_letter').val(1); // Se establecen valores en 0 para que no se actualize el no Correspondencia
    $('#myForm').submit();
}