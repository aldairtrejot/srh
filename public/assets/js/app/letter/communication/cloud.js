
//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

//Inicio de variables
$(document).ready(function () {
    $(window).click(function (event) {
        if ($(event.target).is('#id_modal_delete_oficio')) {
            $('#id_modal_delete_oficio').fadeOut(); // Ocultar la ventana modal
        }
    });
});

// LA funcion habilita el modal asi como defina las variables
function openModalOificio(uid) {
    $('#id_modal_delete_oficio').fadeIn();//Iniciar ventana modal
    $('#id_uuid_oficio').val(uid); // Se estable el uuid en una variable de modal
}

// La función elimina en alfrsco el uuid del doc, asi como actualiza la tabla dejandola vacia
function confirmModalOficio() {
    $.ajax({
        url: URL_DEFAULT.concat('/communication/updateOficio'),
        type: 'POST',
        data: {
            uuid: $('#id_uuid_oficio').val(),
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            // Validación de mensage de exito o error
            if (response.status) {
                notyfEM.success("El archivo se eliminó correctamente.");
            } else {
                notyfEM.error("Algo inesperado ocurrió al realizar la acción.");
            }
            $('#id_modal_delete_oficio').fadeOut(); // Cerrar la ventana modal
            searchInit(); // Ejecucion de tabla para actualizacion de cambios
        },
    });
}

// OCULTA MODAL
$('#id_modal_calcel_oficio').click(function () { //Se pulsa el boton de cancelar
    $('#id_modal_delete_oficio').fadeOut(); // Cerrar la ventana modal
});
