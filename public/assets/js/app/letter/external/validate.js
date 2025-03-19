// Validacion de fórmulario
document.getElementById("myForm").addEventListener("submit", function (event) {
    if (//Validacion de campos requeridos y max caracteres
        isFieldEmpty($('#id_cat_dependencia').val(), 'Dependencia') ||
        isFieldEmpty($('#id_cat_dependencia_area').val(), 'Área') ||
        isFieldEmpty($('#fecha_documento').val(), 'Fecha de documento') ||
        isFieldEmpty($('#no_documento').val(), 'No. Documento') ||
        isFieldEmpty($('#asunto').val(), 'Asunto') ||
        isFieldEmpty($('#observaciones').val(), 'Observaciones') ||
        isExceedingLength($('#asunto').val(), 'Asunto', 300) ||
        isExceedingLength($('#observaciones').val(), 'Observaciones', 190)) {
        event.preventDefault();  // Evita el envío del formulario
        return;  // Detener la ejecución aquí
    }

    let isValid = getOnlyExternal();
    if (isValid) {
        notyfEM.error('Ya existe el No. Documento');
        event.preventDefault();  // Detener el envío si la validación falla
        return;  // Detener la ejecución aquí
    }

});

// La funcion valida que solo exista un no_documento unico
function getOnlyExternal() {
    let isValid = false;  // Asumimos que es inválido inicialmente

    $.ajax({
        url: URL_DEFAULT.concat('/external/unique'),
        type: 'POST',
        async: false, // Asegura que la ejecución sea sincrónica
        data: {
            id: $('#id_tbl_circular_externa').val(),
            no_documento: $('#no_documento').val(),
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            let item = response.status;
            if (item) {
                isValid = true;  // La validación fue exitosa
            }
        }
    });

    return isValid;  // Regresa el resultado de la validación
}