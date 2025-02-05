// Validacion de fórmulario
document.getElementById("myForm").addEventListener("submit", function (event) {
    let fecha_inicio = document.getElementById('fecha_inicio').value;
    let fecha_fin = document.getElementById('fecha_fin').value;

    if (//Validacion de campos requeridos y max caracteres
        isFieldEmpty($('#fecha_inicio').val(), 'Fecha de emición') ||
        isFieldEmpty($('#fecha_fin').val(), 'Fecha de aplicación') ||
        isFieldEmpty($('#asunto').val(), 'Asunto') ||
        isFieldEmpty($('#destinatario').val(), 'Destinatario') ||
        isFieldEmpty($('#observaciones').val(), 'Observaciones') ||
        isExceedingLength($('#asunto').val(), 'Asunto', 300) ||
        isExceedingLength($('#destinatario').val(), 'Destinatario', 190) ||
        isExceedingLength($('#observaciones').val(), 'Observaciones', 140)) {
        event.preventDefault();  // Evita el envío del formulario
        return;  // Detener la ejecución aquí
    }

    if ($('#es_por_area').val()) {//Validacion de check activo
        if (isFieldEmpty($('#id_cat_area_documento').val(), 'Área') ||
            isFieldEmpty($('#id_usuario_area').val(), 'Usuario') ||
            isFieldEmpty($('#id_usuario_enlace').val(), 'Enlace')) { // valida que el campo este seleccionado
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

    /*
    // Validar que la fecha de inicio no sea menor a la fecha de fin
    if (fecha_inicio > fecha_fin) {
        notyfEM.error("La fecha de inicio no puede ser mayor a la fecha de fin.");
        event.preventDefault();
        return; // Detener la ejecución aquí
    }
        */

    $('#num_documento_area').prop('disabled', false); //desabilitar contenid
});

/*
//Validacion cuando se cambia el evento de fecha
$('#fecha_inicio').change(function () {
    validateDate();
});

//Validacion cuando se cambia el evento de fecha
$('#fecha_fin').change(function () {
    validateDate();
});

//La funcion valida que la fecha de inicio no sea mayor a la fecha de fin
function validateDate() {
    let fecha_inicio = document.getElementById('fecha_inicio').value;
    let fecha_fin = document.getElementById('fecha_fin').value;
    if (fecha_inicio !== '' && fecha_fin !== '') { //Valida que los campos fecchas tengan informacion
        if (fecha_inicio > fecha_fin) {
            notyfEM.error("La fecha de inicio no puede ser mayor a la fecha de fin.");
        }
    }
}*/