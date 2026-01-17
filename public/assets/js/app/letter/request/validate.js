// Validacion de fórmulario
document.getElementById("myForm").addEventListener("submit", function (event) {
    if (//Validacion de campos requeridos y max caracteres
        isFieldEmpty($('#fecha_documento').val(), 'Fecha de documento') ||
        isFieldEmpty($('#fecha_termino').val(), 'Fecha de Termino') ||
        isFieldEmpty($('#asunto').val(), 'Asunto') ||
        isFieldEmpty($('#observaciones').val(), 'Observaciones') ||
        isFieldEmpty($('#id_cat_solicitante').val(), 'Solicitante') ||
        isExceedingLength($('#asunto').val(), 'Asunto', 300) ||
        isExceedingLength($('#observaciones').val(), 'Observaciones', 150)) {
        event.preventDefault();  // Evita el envío del formulario
        return;  // Detener la ejecución aquí
    }
});


