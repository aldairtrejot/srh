
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

    $(window).click(function (event) {
        if ($(event.target).is('#id_modal_delete_acuse')) {
            $('#id_modal_delete_acuse').fadeOut(); // Ocultar la ventana modal
        }
    });
});

// LA funcion habilita el modal asi como defina las variables
function openModalOificio(uid) {
    $('#id_modal_delete_oficio').fadeIn();//Iniciar ventana modal
    $('#id_uuid_oficio').val(uid); // Se estable el uuid en una variable de modal
}


// LA funcion habilita el modal asi como defina las variables
function openModalAcuse(uid) {
    $('#id_modal_delete_acuse').fadeIn();//Iniciar ventana modal
    $('#id_uuid_acuse').val(uid); // Se estable el uuid en una variable de modal
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

// La función elimina en alfrsco el uuid del doc, asi como actualiza la tabla dejandola vacia
function confirmModalAcuse() {
    $.ajax({
        url: URL_DEFAULT.concat('/communication/updateAcuse'),
        type: 'POST',
        data: {
            uuid: $('#id_uuid_acuse').val(),
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            // Validación de mensage de exito o error
            if (response.status) {
                notyfEM.success("El archivo se eliminó correctamente.");
            } else {
                notyfEM.error("Algo inesperado ocurrió al realizar la acción.");
            }
            $('#id_modal_delete_acuse').fadeOut(); // Cerrar la ventana modal
            searchInit(); // Ejecucion de tabla para actualizacion de cambios
        },
    });
}

// OCULTA MODAL
$('#id_modal_calcel_oficio').click(function () { //Se pulsa el boton de cancelar
    $('#id_modal_delete_oficio').fadeOut(); // Cerrar la ventana modal
});


// OCULTA MODAL
$('#id_modal_calcel_acuse').click(function () { //Se pulsa el boton de cancelar
    $('#id_modal_delete_acuse').fadeOut(); // Cerrar la ventana modal
});


// Funcion que se activa al momento de presionar el boton de agregar
function addFileOficio(id) {
    $('#id_oficio').val(id) // Se define el id oculto para su validacion
    $('.file-input-oficio').click(); // Se abre el boton para agregar archivo
}

// Funcion que se activa al momento de presionar el boton de agregar
function addFileAcuse(id) {
    $('#id_acuse').val(id) // Se define el id oculto para su validacion
    $('.file-input-acuse').click(); // Se abre el boton para agregar archivo
}

// Se mandad el archivo al controlador para que se agregue
$('.file-input-oficio').on('change', function (event) {
    let file = event.target.files[0]; // Obtener el primer archivo seleccionado

    if (file) {
        if (file) {
            showSpinner();// Inicio de spinner
            let data = new FormData();// Crear el objeto FormData
            data.append('file', file);
            data.append('id', $('#id_oficio').val());
            $.ajax({
                url: URL_DEFAULT.concat("/communication/addOficio"),
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

// Se mandad el archivo al controlador para que se agregue
$('.file-input-acuse').on('change', function (event) {
    let file = event.target.files[0]; // Obtener el primer archivo seleccionado

    if (file) {
        if (file) {
            showSpinner();// Inicio de spinner
            let data = new FormData();// Crear el objeto FormData
            data.append('file', file);
            data.append('id', $('#id_acuse').val());
            $.ajax({
                url: URL_DEFAULT.concat("/communication/addAcuse"),
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
                        notyfEM.success("Doc. Acuse agregado correctamente.");
                    } else {
                        notyfEM.error(response.messages);
                    }
                    searchInit(); // Ejecucion de tabla para actualizacion de cambios

                    $('.file-input-acuse').val('');
                    $('#id_acuse').val('');
                },
            });
        }
    }
});