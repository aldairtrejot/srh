
// El codigo de la clase asigna el modal de copíar para mas zonas de correspondencia principal
//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

// Carga de formulario inicial
$(document).ready(function () {

    // Refresh add solicitante
    $(window).click(function (event) {
        if ($(event.target).is('#modalCopy')) {
            $('#modalCopy').fadeOut(); // Ocultar la ventana modal
            cleanSelectCopy(); // Ocultar input de add copy
        }
    });

    $(window).click(function (event) {
        if ($(event.target).is('#id_modal_delete_acuse')) {
            $('#id_modal_delete_acuse').fadeOut(); // Ocultar la ventana modal
            cleanSelectCopy(); // Ocultar input de add copy
        }
    });

    cleanSelectCopy(); // Ocultar input de add copy

});

// ACTIVACION DE MODAL
// LA funcion activa el modal solicitante
function openCopy(id, folGestion) {
    $('#name_folio_gestion').text(folGestion); // Se establece la variable en el texto
    $('#id_correspondencia_x').val(id)// Se agrega el id de correspondencia interno
    $('#modalCopy').fadeIn();//Iniciar ventana modal
    searchInitToCopy(id); // Funcion de tabla
    cleanSelectCopy(); // se oculta agregar de coppy
}

// Funcion para eliminar el elemento
function openModalDelete(id) {
    // Validación por ROLE
    let bool_user_role = $('#bool_user_role').val(); //Se obtienen los roles de usuario
    let new_variable = (bool_user_role && bool_user_role.trim() !== '') ? true : false; //Se validan para obtener una variable boolean
    if (!new_variable) { //Condicion para inabilitar las opciones
        notyfEM.error('No se han configurado permisos para este usuario.')
    } else {
        $('#id_delete').val(id); //Se declaran valorea
        $('#id_modal_delete_acuse').fadeIn();//Iniciar ventana modal
    }
}


// OCULTA MODAL
// Cerrar modal refresh no oficio
$('#cancel_copy').click(function () { //Se pulsa el boton de cancelar
    $('#modalCopy').fadeOut(); // Cerrar la ventana modal
});

// Cerrar modal refresh no oficio
$('#id_modal_calcel_acuse').click(function () { //Se pulsa el boton de cancelar
    $('#id_modal_delete_acuse').fadeOut(); // Cerrar la ventana modal
});


// GUARDAR CONTENIDO
// Guardar o validar contenido de No oficio
function confirmarCopy() {
    $('#modalCopy').fadeOut(); // Cerrar la ventana modal
}

// Elimina el contenido, cuando el usuarioconfirma la accion
function confirmModalDelete() {
    $.ajax({
        url: URL_DEFAULT.concat('/letter/delete/copy'),
        type: 'POST',
        data: {
            id: $('#id_delete').val(),
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            if (response.value) {
                notyfEM.success("Elemento eliminado correctamente.");
            } else {
                notyfEM.error('Ocurrió un error inesperado al intentar eliminar el elemento.');
            }
            searchInitToCopy($('#id_correspondencia_x').val()); // Funcion de tabla
            $('#id_modal_delete_acuse').fadeOut(); // Ocultar la ventana modal
        },
    });
}


// LA funcion muestra el click que se le da al boton agregar registro
function addCopy() {
    getSelectAreaCopy(); // Se inicia el catalogo de area
    showDiv('mostrar_ocultar_copy');
    //hideDiv('mostrar_ocultar_template')
}

// La funcion oculta el div de copy
function hiddenCopy() {
    cleanSelectCopy(); // Ocultar input de add copy
}

// Inicio de select de area
function getSelectAreaCopy() {
    $.ajax({
        url: URL_DEFAULT.concat('/letter/collection/area'),
        type: 'POST',
        data: {
            id: $('#id_delete').val(),
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            foreachSelect(response.result, '#id_cat_area_copy');
        },
    });
}


//Codigo para la seleccion de area y como cambia el valor de los demas select que dependen de ella
$('#id_cat_area_copy').on('change', function () {
    let idValue = $(this).val();  // Obtiene el valor de la opción seleccionada
    if (idValue) { // Realiza la solicitud AJAX solo si se ha seleccionado un valor
        $.ajax({
            url: URL_DEFAULT.concat('/letter/collection/collectionArea'),
            type: 'POST',
            data: {
                id: idValue,
                _token: token  // Usar el token extraído de la metaetiqueta
            },
            success: function (response) {
                //proceso de select 
                foreachSelectNull(response.selectEnlace, '#id_usuario_enlace_copy');
                foreachSelectNull(response.selectUsuario, '#id_usuario_area_copy');
                foreachSelect(response.selectTramite, '#id_cat_tramite_copy');
            },
        });
    } else {
        cleanSelectMoreSelect('#id_usuario_enlace_copy'); //Se limpia el select
        cleanSelectMoreSelect('#id_usuario_area_copy'); //Se limpia el select
        cleanSelectMoreSelect('#id_cat_tramite_copy'); //Se limpia el select
        cleanSelectMoreSelect('#id_cat_clave_copy'); //Se limpia el select
    }
});

//Codigo para la seleccion de Trmaite y como cambia el valor de los demas select que dependen de ella
$('#id_cat_tramite_copy').on('change', function () {
    let idValue = $(this).val();  // Obtiene el valor de la opción seleccionada
    if (idValue) { // Realiza la solicitud AJAX solo si se ha seleccionado un valor
        $.ajax({
            url: URL_DEFAULT.concat('/letter/collection/collectionTramite'),
            type: 'POST',
            data: {
                id: idValue,
                _token: token  // Usar el token extraído de la metaetiqueta
            },
            success: function (response) {
                //Proceso de select
                foreachSelectNull(response.selectClave, '#id_cat_clave_copy');
            },
        });
    } else {
        cleanSelectMoreSelect('#id_cat_clave_copy'); //Se limpia el select
    }
});


// LA funcion guarda y valida las copias de correspondencia
function saveCopy() {

    if (isFieldEmpty($('#id_cat_area_copy').val(), 'Área') ||
        isFieldEmpty($('#id_usuario_area_copy').val(), 'Usuario') ||
        isFieldEmpty($('#id_usuario_enlace_copy').val(), 'Enlace') ||
        isFieldEmpty($('#id_cat_tramite_copy').val(), 'Tramite') ||
        isFieldEmpty($('#id_cat_clave_copy').val(), 'Clave')) {
    } else {
        onlyArea($('#id_correspondencia_x').val(), $('#id_cat_area_copy').val());
        //validateIsOK(); // save add
    }
}

function onlyArea(id_tbl_correspondencia, id_cat_area) {
    $.ajax({
        url: URL_DEFAULT.concat('/letter/validateCopy'),
        type: 'POST',
        data: {
            id_tbl_correspondencia: id_tbl_correspondencia,
            id_cat_area: id_cat_area,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            if (response.result) {
                validateIsOK(); // save add
            } else {
                notyfEM.error("El área ya tiene asignado el folio de gestión.");
            }
        },
    });
}

// La funcion guarda los datos, una ves que se han validado con exito
function validateIsOK() {
    $.ajax({
        url: URL_DEFAULT.concat('/letter/saveCopy'),
        type: 'POST',
        data: {
            id_tbl_correspondencia: $('#id_correspondencia_x').val(),
            id_cat_area: $('#id_cat_area_copy').val(),
            id_usuario_area: $('#id_usuario_area_copy').val(),
            id_usuario_enlace: $('#id_usuario_enlace_copy').val(),
            id_cat_tramite: $('#id_cat_tramite_copy').val(),
            id_cat_clave: $('#id_cat_clave_copy').val(),
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            response.result ? notyfEM.success("Elemento agregado correctamente.") : notyfEM.error('Ocurrió un error inesperado al intentar agregar el elemento.');
            searchInitToCopy($('#id_correspondencia_x').val()); // Funcion de tabla copy
            cleanSelectCopy(); // Ocultar input de add copy
        },
    });
}

function cleanSelectCopy() {
    hideDiv('mostrar_ocultar_copy');
    cleanSelectMoreSelect('#id_cat_area_copy');// Se limpia el select
    cleanSelectMoreSelect('#id_usuario_area_copy');// Se limpia el select
    cleanSelectMoreSelect('#id_usuario_enlace_copy');// Se limpia el select
    cleanSelectMoreSelect('#id_cat_tramite_copy');// Se limpia el select
    cleanSelectMoreSelect('#id_cat_clave_copy');// Se limpia el select
}