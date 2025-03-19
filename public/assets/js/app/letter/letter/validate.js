// Validacion de fórmulario

document.getElementById("myForm").addEventListener("submit", function (event) {
    let fecha_inicio = document.getElementById('fecha_inicio').value;
    let fecha_fin = document.getElementById('fecha_fin').value;

    let bool_user_role = $('#bool_user_role').val(); //Se obtienen los roles de usuario
    let new_variable = (bool_user_role && bool_user_role.trim() !== '') ? true : false;

    if (new_variable) {
        if (
            //isPositiveInteger($('#num_flojas').val(), 'No. hojas') ||
            isFieldEmpty($('#num_documento').val(), 'No. Documento') ||
            isFieldEmpty($('#fecha_inicio').val(), 'Fecha de inicio') ||
            isFieldEmpty($('#fecha_fin').val(), 'Fecha fin') ||
            //isFieldEmpty($('#num_flojas').val(), 'No. hojas') ||
            isFieldEmpty($('#folio_gestion').val(), 'Folio de gestión') ||
            isFieldEmpty($('#fecha_documento').val(), 'Fecha de doc.') ||
            isFieldEmpty($('#id_cat_entidad').val(), 'Entidad') ||
            isFieldEmpty($('#asunto').val(), 'Asunto') ||
            isFieldEmpty($('#id_cat_area').val(), 'Área') ||
            isFieldEmpty($('#id_usuario_area').val(), 'Usuario') ||
            isFieldEmpty($('#id_usuario_enlace').val(), 'Enlace') ||
            isFieldEmpty($('#id_cat_unidad').val(), 'Unidad') ||
            isFieldEmpty($('#id_cat_coordinacion').val(), 'Coordinación') ||
            isFieldEmpty($('#id_cat_estatus').val(), 'Estatus') ||
            isFieldEmpty($('#id_cat_tramite').val(), 'Tramite') ||
            isFieldEmpty($('#id_cat_clave').val(), 'Clave') ||
            isFieldEmpty($('#puesto_remitente').val(), 'Puesto remitente') ||
            isExceedingLength($('#folio_gestion').val(), 'Folio de gestión', 50) ||
            isExceedingLength($('#puesto_remitente').val(), 'Puesto remitente', 100) ||
            isExceedingLength($('#num_documento').val(), 'No. Documento', 50) ||
            isExceedingLength($('#asunto').val(), 'Asunto', 400) ||
            isExceedingLength($('#observaciones').val(), 'Observaciones', 140)) {
            event.preventDefault();  // Evita el envío del formulario
            return;  // Detener la ejecución aquí
        }

        // Valida el check de agregar remitentes, mas de dos
        if ($('#son_mas_remitentes').val()) {
            console.log('intro to remitentes');
            if (isFieldEmpty($('#remitente').val(), 'Remitente') ||
                isExceedingLength($('#remitente').val(), 'Remitente', 230)) {
                event.preventDefault();  // Evita el envío del formulario
                return;  // Detener la ejecución aquí
            }
        }

        if (!$('#son_mas_remitentes').val() && !$('#rfc_remitente_bool').val()) {
            console.log('es un remitente');
            if (isFieldEmpty($('#id_cat_remitente').val(), 'Remitente')) {
                event.preventDefault();  // Evita el envío del formulario
                return;  // Detener la ejecución aquí
            }
        }

        //Validacion para agregar remitente al sistema
        if ($('#rfc_remitente_bool').val()) {
            if (isFieldEmpty($('#remitente_nombre').val(), 'Nombre') ||
                isFieldEmpty($('#remitente_apellido_paterno').val(), 'Apellido paterno') ||
                isFieldEmpty($('#remitente_apellido_materno').val(), 'Unidad') ||
                isExceedingLength($('#remitente_nombre').val(), 'Apellido materno', 50) ||
                isExceedingLength($('#remitente_apellido_paterno').val(), 'Apellido paterno', 50) ||
                isExceedingLength($('#remitente_apellido_materno').val(), 'Apellido materno', 50) ||
                isExceedingLength($('#remitente_rfc').val(), 'RFC', 13)) {
                event.preventDefault();  // Evita el envío del formulario
                return;  // Detener la ejecución aquí
            }

            //Valida la estructura que el rfc sea correcta
            if ($('#remitente_rfc').val() !== '') {
                if (!validateRfc($('#remitente_rfc').val())) {
                    notyfEM.error('El RFC de remitente no es valido.');
                    event.preventDefault();  // Detener el envío si la validación falla
                    return;  // Detener la ejecución aquí
                }
            }

            // Validacion de nombre unico de remitente
            let isValidN = getUniqueNameRemitente($('#remitente_nombre').val(), $('#remitente_apellido_paterno').val(), $('#remitente_apellido_materno').val(), 'nombre');
            if (isValidN) {
                notyfEM.error('El Nombre de remitente ya está registrado.');
                event.preventDefault();  // Detener el envío si la validación falla
                return;  // Detener la ejecución aquí
            }

            // Validacion de rfc unico de remitente
            let isValidR = getUniqueRemitente($('#remitente_rfc').val(), 'rfc');
            if (isValidR) {
                notyfEM.error('El RFC de remitente ya está registrado.');
                event.preventDefault();  // Detener el envío si la validación falla
                return;  // Detener la ejecución aquí
            }

        }

        // Valida que si las fechas son iguales sean requeridas las horas
        if (fecha_inicio == fecha_fin) {
            if (isFieldEmpty($('#horas_respuesta').val(), 'Horas respuesta')) {
                event.preventDefault();  // Evita el envío del formulario
                return;  // Detener la ejecución aquí
            }
        }

        // Validar que la fecha de inicio no sea menor a la fecha de fin
        if (fecha_inicio > fecha_fin) {
            notyfEM.error("La fecha de inicio no puede ser mayor a la fecha de fin.");
            event.preventDefault();
            return; // Detener la ejecución aquí
        }

        /*
        // Validacion de no document sea unico
        let isValid = getNoUnique($('#id_tbl_correspondencia').val(), $('#num_documento').val(), 'num_documento');
        if (isValid) {
            notyfEM.error('El No. Documento ya está registrado.');
            event.preventDefault();  // Detener el envío si la validación falla
            return;  // Detener la ejecución aquí
        }*/

        let isValidG = getNoUnique($('#id_tbl_correspondencia').val(), $('#folio_gestion').val(), 'folio_gestion');
        if (isValidG) {
            notyfEM.error('El Folio de gestión ya está registrado.');
            event.preventDefault();  // Detener el envío si la validación falla
            return;  // Detener la ejecución aquí
        }
    } else {
        // Role not administration
        if (
            isFieldEmpty($('#id_cat_estatus').val(), 'Estatus') ||
            isExceedingLength($('#observaciones').val(), 'Observaciones', 140)) {
            event.preventDefault();  // Evita el envío del formulario
            return;  // Detener la ejecución aquí
        }
    }

    $('#id_cat_estatus').prop('disabled', false); //desabilitar contenido
    $('#id_cat_area').prop('disabled', false); //desabilitar contenido
    $('#num_documento').prop('disabled', false); //desabilitar contenido
    $('#folio_gestion').prop('disabled', false); //desabilitar contenido
    $('#asunto').prop('disabled', false); //desabilitar contenido
    $('#observaciones').prop('disabled', false); //desabilitar contenido
});

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
}