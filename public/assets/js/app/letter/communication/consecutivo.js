
// Funciones para iterador o no de oficio

//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

//Cambops
// La función actualiza el no de oficio, si es que ya se ha asignado uno
function updateIterator(status) {
    $('#modalBackdrop').fadeOut(); // Cerrar la ventana modal
    $.ajax({
        url: URL_DEFAULT.concat('/communication/noOficio'),
        type: 'POST',
        data: {
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {

            let item = response;
            $('#_labNoOficio').text(item.result); // establecer los valores en label
            $('#consecutivo').val(item.result); // establecer los valores en input

            // Si se pasa el parametro de verdadero se manda el msj, de lo contrario no se manda nada
            if (status) {
                notyfEM.success("No. Oficio actualizado."); // status success
            }

        },
    });
}