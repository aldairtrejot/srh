
// Codigo para la implementacioon de reporte de exel
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form


// Carga de formulario inicial
$(document).ready(function () {
    // Refresh add solicitante
    $(window).click(function (event) {
        if ($(event.target).is('#modalCopy')) {
            $('#modalCopy').fadeOut(); // Ocultar la ventana modal
        }
    });
});

// La función abre el modal de reporte
function openModal() {
    console.log('intro');
    $('#modalCopy').fadeIn();//Iniciar ventana modal
}

// Cerrar modal refresh no oficio
$('#cancel_copy').click(function () { //Se pulsa el boton de cancelar
    $('#modalCopy').fadeOut(); // Cerrar la ventana modal
});
