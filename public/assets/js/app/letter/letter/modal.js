
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
            hideDiv('mostrar_ocultar_copy'); // Ocultar input de add copy
        }
    });

    $(window).click(function (event) {
        if ($(event.target).is('#id_modal_delete_acuse')) {
            $('#id_modal_delete_acuse').fadeOut(); // Ocultar la ventana modal
            hideDiv('mostrar_ocultar_copy'); // Ocultar input de add copy
        }
    });

    hideDiv('mostrar_ocultar_copy'); // Ocultar input de add copy
});

// ACTIVACION DE MODAL
// LA funcion activa el modal solicitante
function openCopy(id, folGestion) {
    $('#name_folio_gestion').text(folGestion); // Se establece la variable en el texto
    $('#id_correspondencia_x').val(id)// Se agrega el id de correspondencia interno
    $('#modalCopy').fadeIn();//Iniciar ventana modal
    searchInitToCopy(id); // Funcion de tabla
}

// Funcion para eliminar el elemento
function openModalDelete(id) {
    $('#id_delete').val(id); //Se declaran valorea
    $('#id_modal_delete_acuse').fadeIn();//Iniciar ventana modal
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
            console.log(response);
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
    showDiv('mostrar_ocultar_copy');
    //hideDiv('mostrar_ocultar_template')
}

// La funcion oculta el div de copy
function hiddenCopy() {
    hideDiv('mostrar_ocultar_copy'); // Ocultar input de add copy
}

// LA funcion guarda y valida las copias de correspondencia
function saveCopy() {
    console.log('success');
}

// Inicio de select de area
function getSelectAreaCopy() {
    
}