// La funcion detecta cuando el usuario toca el boton de password
//Token
var token_password = $('meta[name="csrf-token"]').attr('content'); //Token for form

//Inicio de variables
$(document).ready(function () {
    $(window).click(function (event) {
        if ($(event.target).is('#_modalChangePassword')) {
            $('#_modalChangePassword').fadeOut(); // Ocultar la ventana modal
        }
    });
});

//Detecta el cambio de evento cuando se pulsa el boton de password
$("#changePassword").click(function () {
    changePassword();
});


//La funcion elimina un documento
function changePassword() {
    cleanPassword(); // Limpiar modal de password
    $('#_modalChangePassword').fadeIn();//Iniciar ventana modal

    $('#_cancelAction').click(function () { //Se pulsa el boton de cancelar
        $('#_modalChangePassword').fadeOut(); // Cerrar la ventana modal
    });
}

//La funcion valida la contraseña ingresada
function validatePassword() {
    let bool = true;
    if (//Validacion de campos requeridos y max caracteres
        isFieldEmpty($('#oldPassword').val(), 'Contraseña anterior') ||
        isFieldEmpty($('#newPassword').val(), 'Nueva contraseña') ||
        isFieldEmpty($('#confirmPassword').val(), 'Confirmar contraseña') ||
        isExceedingLength($('#newPassword').val(), 'Nueva contraseña', 40) ||
        isExceedingLength($('#confirmPassword').val(), 'Confirmar contraseña', 40) ||
        isExceedingMinLength($('#newPassword').val(), 'Nueva contraseña', 5) ||
        isExceedingMinLength($('#confirmPassword').val(), 'Confirmar contraseña', 5) ||
        validateEqual($('#newPassword').val(), $('#confirmPassword').val())) {
        bool = false;
        event.preventDefault();  // Evita el envío del formulario
        return;  // Detener la ejecución aquí
    }

    let isValidPW = getOldPassword($('#oldPassword').val());
    if (!isValidPW) {
        bool = false;
        notyfEM.error('La contraseña anterior no es correcta.');
        event.preventDefault();  // Detener el envío si la validación falla
        return;  // Detener la ejecución aquí
    }

    if (bool) { //Guarda la contraseña
        savePassword();
    }
}

//La funcion actualiza la contraseña, para que se actuañize
function savePassword() {
    $.ajax({
        url: URL_DEFAULT.concat('/user/changePassword'),
        type: 'POST',
        async: false, // Asegura que la ejecución sea sincrónica
        data: {
            value: $('#newPassword').val(),
            _token: token_password  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            let item = response.value;
            if (item) {
                notyfEM.success("Contraseña actualizada");
            } else {
                notyfEM.error("Algo inesperado paso");
            }
            $('#_modalChangePassword').fadeOut();
        }
    });
}

// Valida que la pw anterior sea la correscta
function getOldPassword(value) {
    let isValid = false;  // Asumimos que es inválido inicialmente

    $.ajax({
        url: URL_DEFAULT.concat('/user/validatePassword'),
        type: 'POST',
        async: false, // Asegura que la ejecución sea sincrónica
        data: {
            value: value,
            _token: token_password  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            let item = response.value;
            if (item) {
                isValid = true;  // La validación fue exitosa
            }
        }
    });

    return isValid;  // Regresa el resultado de la validación
}

// Limpiar los input de password
function cleanPassword() {
    $('#oldPassword').val('');
    $('#newPassword').val('');
    $('#confirmPassword').val('');
}
