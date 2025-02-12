
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

    // Refresh add solicitante
    $(window).click(function (event) {
        if ($(event.target).is('#modalSolicitante')) {
            $('#modalSolicitante').fadeOut(); // Ocultar la ventana modal
        }
    });
});

// ACTIVACION DE MODAL
// LA funcion activa el modal solicitante
function addSolicitante() {
    $('#modalSolicitante').fadeIn();//Iniciar ventana modal
}

// LA funcion activa el modal solicitante
function refresNota() {
    $('#modalBackdrop').fadeIn();//Iniciar ventana modal
}


// OCULTA MODAL
// Cerrar modal refresh no oficio
$('#cancelBtn').click(function () { //Se pulsa el boton de cancelar
    $('#modalBackdrop').fadeOut(); // Cerrar la ventana modal
});

// Cerrar modal refresh SOLICITANTE
$('#cancelBtn_solicitante').click(function () { //Se pulsa el boton de cancelar
    $('#modalSolicitante').fadeOut(); // Cerrar la ventana modal
});

// GUARDAR CONTENIDO
// Guardar o validar contenido de No oficio
function confirmRefreshOficio() {
    updateIterator(true); // Ser establece la variable en verdadero para mandar mensaje de exito, si es falso no manda mensaje
}

// Guardar o validar contenido Solicitante
function confirmSolicitante() {
    validateSolicitante('#id_cat_solicitante'); // Se ejecuta función de solicitante
}