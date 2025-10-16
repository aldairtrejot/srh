// Variable de token
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

// Inicio de eventos y funciones
$(document).ready(function () {
    // Oculta modal de correo, cuando se toque fuera de la pantalla
    $(window).click(function (event) {
        if ($(event.target).is('#_modalChangeMail')) {
            $('#_modalChangeMail').fadeOut(); // Ocultar la ventana modal
        }
    });
});

function opneEmail(id, value) {
    //sendEmailLetter(id, value, 'nameUser', 'mail');
    cleanEmail(); // Limpiar modal de email
    $('#_modalChangeMail').fadeIn();//Iniciar ventana modal
    $('#noTurnoSistemaEmail').text(value); // Se inciia el num de turno en modal
    $('#id_tbl_correspondencia_email').val(id); // Inicio de valor de input -> id

    $.ajax({
        url: URL_DEFAULT.concat('/letter/email/data'),
        type: 'POST',
        data: {
            id: $('#id_tbl_correspondencia_email').val(),
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            if (response.status) {
                $('#emailName').val(response.name);
                $('#emailMail').val(response.email);
            } else {
                $('#_modalChangeMail').fadeOut();
                notyfEM.error("El usuario no cuenta con roles para esta acción");
            }

        },
    });

    $('#_cancelEmail').click(function () { //Se pulsa el boton de cancelar
        $('#_modalChangeMail').fadeOut(); // Cerrar la ventana modal
    });
}

//La funcion valida los campos de modal mail
function validateEmail() {
    let bool = true; // Validación para que se mande la respuesta o no 
    if (//Validacion de campos requeridos y max caracteres
        isFieldEmpty($('#emailName').val(), 'Nombre') ||
        isExceedingLength($('#emailName').val(), 'Nombre', 40) ||
        isExceedingLength($('#observaciones').val(), 'Observaciones', 150) ||
        validateMail($('#emailMail').val())) {
        bool = false;
    }

    if (bool) {
        sendEmailLetter(); // Se ejecuta la función de mandar el email
    }
}

// La función limpia los valores del modal email
function cleanEmail() {
    $('#noTurnoSistemaEmail').val('');
    $('#emailName').val('');
    $('#emailMail').val('');
    $('#observaciones').val('');
}

// La función manda al contro
function sendEmailLetter() {
    showSpinner();// Inicio de spinner
    $('#_modalChangeMail').fadeOut(); // Cerrar la ventana modal
    $.ajax({
        url: URL_DEFAULT.concat('/letter/email'),
        type: 'POST',
        data: {
            id: $('#id_tbl_correspondencia_email').val(),
            nameUser: $('#emailName').val(),
            mail: $('#emailMail').val(),
            observaciones: $('#observaciones').val(),
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            // Acceder al valor del 'status'
            hideSpinner(); // Se oculta el spinner
            searchInit();
            if (response.status) {
                notyfEM.success("Email enviado de manera exitosa.");
            } else {
                notyfEM.error("Se produjo un error al intentar enviar el email.");
            }
        },
    });
}