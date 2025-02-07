// Validacion de fórmulario
document.getElementById("myForm").addEventListener("submit", function (event) {
    let fecha_inicio = document.getElementById('fecha_inicio').value;
    let fecha_fin = document.getElementById('fecha_fin').value;

    if (//Validacion de campos requeridos y max caracteres
        isFieldEmpty($('#fecha_inicio').val(), 'Fecha de inicio') ||
        isFieldEmpty($('#asunto').val(), 'Asunto') ||
        isFieldEmpty($('#observaciones').val(), 'Observaciones') ||
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

    // Validar que la fecha de inicio no sea menor a la fecha de fin
    if (fecha_inicio > fecha_fin) {
        notyfEM.error("La fecha de inicio no puede ser mayor a la fecha de fin.");
        event.preventDefault();
        return; // Detener la ejecución aquí
    }

    $('#num_documento_area').prop('disabled', false); //desabilitar contenido
});

