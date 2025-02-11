// Validacion de fórmulario
document.getElementById("myForm").addEventListener("submit", function (event) {
    if (//Validacion de campos requeridos y max caracteres
        isFieldEmpty($('#fecha_documento').val(), 'Fecha de documento') ||
        isFieldEmpty($('#asunto').val(), 'Asunto') ||
        isFieldEmpty($('#id_cat_destinatario').val(), 'Destinatario') ||
        isFieldEmpty($('#id_cat_solicitante').val(), 'Elaboro') ||
        isFieldEmpty($('#id_cat_solicitante_2').val(), 'De') ||
        isExceedingLength($('#asunto').val(), 'Asunto', 300)) {
        event.preventDefault();  // Evita el envío del formulario
        return;  // Detener la ejecución aquí
    }
});


