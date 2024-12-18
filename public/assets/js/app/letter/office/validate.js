// Validacion de fórmulario
document.getElementById("myForm").addEventListener("submit", function (event) {
    if (//Validacion de campos requeridos y max caracteres
        isFieldEmpty($('#fecha_inicio').val(), 'Fecha de inicio') ||
        isFieldEmpty($('#asunto').val(), 'Asunto') ||
        isFieldEmpty($('#observaciones').val(), 'Observaciones') ||
        isFieldEmpty($('#id_cat_area').val(), 'Área') ||
        isFieldEmpty($('#id_usuario_area').val(), 'Usuario') ||
        isFieldEmpty($('#id_usuario_enlace').val(), 'Enlace') ||
        isFieldEmpty($('#id_cat_remitente').val(), 'Remitente') ||
        isExceedingLength($('#asunto').val(), 'Asunto', 100) ||
        isExceedingLength($('#observaciones').val(), 'Observaciones', 100)) {
        event.preventDefault();  // Evita el envío del formulario
        return;  // Detener la ejecución aquí
    }

    if ($('#es_por_area').val()) {//Validacion de check activo
        if (isFieldEmpty($('#id_cat_area_documento').val(), 'Área')) { // valida que el campo este seleccionado
            event.preventDefault();  // Evita el envío del formulario
            return;  // Detener la ejecución aquí
        }
    } else {
        let isValid = getNoCorrespondencia($('#num_correspondencia').val());
        if (isValid) {
            notyfEM.error('El No. Correspondencia no se encuentra asociado');
            event.preventDefault();  // Detener el envío si la validación falla
            return;  // Detener la ejecución aquí
        }
    }

$('#num_documento_area').prop('disabled', false); //desabilitar contenido
});

