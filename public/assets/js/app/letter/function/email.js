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

    $('#_cancelEmail').click(function () { //Se pulsa el boton de cancelar
        $('#_modalChangeMail').fadeOut(); // Cerrar la ventana modal
    });
}

//La funcion valida los campos de modal mail
function validateEmail() {

}

// La función limpia los valores del modal email
function cleanEmail() {
    $('#noTurnoSistemaEmail').val('');
    $('#emailName').val('');
    $('#emailMail').val('');
}

function sendEmailLetter(id, value, nameUser, mail) {
    $.ajax({
        url: URL_DEFAULT.concat('/letter/email'),
        type: 'POST',
        data: {
            value: value,
            id: id,
            nameUser: nameUser,
            mail: mail,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            console.log(response);
        },
    });
}