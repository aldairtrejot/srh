//Scrip que se ejecuta con el formulario, para funciones u herramientas extras
//Ejecucion cuando carga el formulario
var token = $('meta[name="csrf-token"]').attr('content'); //Token for form

// La funcion hace validaciones del solicitante
// Esperando como parametros el id, del catalogo solicitante para asignalo en automatico al select
function validateSolicitante(id_solicitante) {

    validateNameSol(id_solicitante); // Validación de nombre unico de remitente
}


// La función valida que el nombre de solicitante sea unico
function validateNameSol(id_solicitante) {
    $.ajax({
        url: URL_DEFAULT.concat('/solicitante/add'),
        type: 'POST',
        data: {
            name: $('#nombreSolicitante').val(),
            firstName: $('#primerApellidoSolicitante').val(),
            seconName: $('#segundoApellidoSolicitante').val(),
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            if (!response.estatus) { // Validacón que el solicitante
                // Actualización automatica de select 
                foreachSelectByDefault(response.solicitanteEdit, response.solicitanteAll, id_solicitante);
                $('#modalSolicitante').fadeOut(); // Cerrar la ventana modal
                notyfEM.error('Solicitante agregado exitosamente.'); // Mensaje de exito
            } else {
                notyfEM.error('El solicitante ya ha sido registrado.'); // Mensaje de error
            }
            /*
            let result = response.result;
            console.log(result.estatus);
            */
            /*
            let item = response;
            $('#_labNoOficio').text(item.result); // establecer los valores en label
            $('#consecutivo').val(item.result); // establecer los valores en input

            // Si se pasa el parametro de verdadero se manda el msj, de lo contrario no se manda nada
            if (message) {
                notyfEM.success("No. Oficio actualizado."); // message success
            }*/

        },
    });
}