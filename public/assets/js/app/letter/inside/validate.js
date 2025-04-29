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

    if ($('#num_correspondencia').val() !== '') {
        let isValid = getNoCorrespondencia($('#num_correspondencia').val());
        if (isValid) {
            notyfEM.error('El Folio de gestión asociado no existe');
            event.preventDefault();  // Detener el envío si la validación falla
            return;  // Detener la ejecución aquí
        }
    }

    let isValidG = getNoUniqueFol($('#id_tbl_interno').val(), $('#num_correspondencia').val());
    if (isValidG) {
        notyfEM.error('El Folio de gestión ya encuentra asociado.');
        event.preventDefault();  // Detener el envío si la validación falla
        return;  // Detener la ejecución aquí
    }
    /*
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

    // Validar que la fecha de inicio no sea menor a la fecha de fin
    if (fecha_inicio > fecha_fin) {
        notyfEM.error("La fecha de inicio no puede ser mayor a la fecha de fin.");
        event.preventDefault();
        return; // Detener la ejecución aquí
    }
*/
    $('#num_documento_area').prop('disabled', false); //desabilitar contenid
});


//Validacion cuando se cambia el evento de fecha
$('#fecha_inicio').change(function () {
    //validateDate();
});

//Validacion cuando se cambia el evento de fecha
$('#fecha_fin').change(function () {
    //validateDate();
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
}

// La funcion valida que el no de documento y el folio de gestion sean unicos
function getNoUniqueFol(id, value) {
    let isValid = false;  // Asumimos que es inválido inicialmente
    if (value !== '') {
        $.ajax({
            url: URL_DEFAULT.concat('/inside/validate/folGestion'),
            type: 'POST',
            async: false, // Asegura que la ejecución sea sincrónica
            data: {
                id: id,
                value: value,
                _token: token  // Usar el token extraído de la metaetiqueta
            },
            success: function (response) {
                console.log(response);
                let item = response.value;
                if (item) {
                    isValid = true;  // La validación fue exitosa
                }
            }
        });
    }
    return isValid;  // Regresa el resultado de la validación
}