//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

// FUNCIONES PARA SOLICITATE
// La funcion valida los campos del solicitante
function validateSolicitante() {
    console.log('sucees');
}

// La función valida que el nombre de solicitante sea unico
function validateName(name) {
    $.ajax({
        url: URL_DEFAULT.concat('/communication/noOficio'),
        type: 'POST',
        data: {
            name: name,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {

            let item = response;
            $('#_labNoOficio').text(item.result); // establecer los valores en label
            $('#consecutivo').val(item.result); // establecer los valores en input

            // Si se pasa el parametro de verdadero se manda el msj, de lo contrario no se manda nada
            if (message) {
                notyfEM.success("No. Oficio actualizado."); // message success
            }

        },
    });
}
