// Validacion de fórmulario

document.getElementById("myForm").addEventListener("submit", function (event) {
    let fecha_inicio = document.getElementById('fecha_inicio').value;
    let fecha_fin = document.getElementById('fecha_fin').value;

    if (
        //isPositiveInteger($('#num_flojas').val(), 'No. hojas') ||
        isFieldEmpty($('#num_documento').val(), 'No. Documento') ||
        isFieldEmpty($('#fecha_inicio').val(), 'Fecha de inicio') ||
        isFieldEmpty($('#fecha_fin').val(), 'Fecha fin') ||
        isFieldEmpty($('#num_flojas').val(), 'No. hojas') ||
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
        isExceedingLength($('#puesto_remitente').val(), 'Puesto remitente', 100) ||
        isExceedingLength($('#num_documento').val(), 'No. Documento', 50) ||
        isExceedingLength($('#lugar').val(), 'Lugar', 100) ||
        isExceedingLength($('#asunto').val(), 'Asunto', 100) ||
        isExceedingLength($('#observaciones').val(), 'Observaciones', 100)) {
        event.preventDefault();  // Evita el envío del formulario
        return;  // Detener la ejecución aquí
    }

    //Validacion para agregar remitente al sistema
    if ($('#rfc_remitente_bool').val()) {
        if (isFieldEmpty($('#remitente_nombre').val(), 'Nombre') ||
            isFieldEmpty($('#remitente_apellido_paterno').val(), 'Apellido paterno') ||
            isFieldEmpty($('#remitente_apellido_materno').val(), 'Unidad') ||
            isFieldEmpty($('#id_cat_area').val(), 'Área') ||
            isExceedingLength($('#remitente_nombre').val(), 'Apellido materno', 50) ||
            isExceedingLength($('#remitente_apellido_paterno').val(), 'Apellido paterno', 50) ||
            isExceedingLength($('#remitente_apellido_materno').val(), 'Apellido materno', 50)) {
            event.preventDefault();  // Evita el envío del formulario
            return;  // Detener la ejecución aquí
        }
    } else {
        if (isFieldEmpty($('#id_cat_remitente').val(), 'Remitente')) {
            event.preventDefault();  // Evita el envío del formulario
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
});