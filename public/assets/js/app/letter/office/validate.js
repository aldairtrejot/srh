// Validacion de fórmulario
document.getElementById("myForm").addEventListener("submit", function (event) {
    if (
        /*
        //isPositiveInteger($('#num_flojas').val(), 'No. hojas') ||
        isFieldEmpty($('#num_documento').val(), 'No. Documento') ||
        isFieldEmpty($('#fecha_inicio').val(), 'Fecha de inicio') ||
        isFieldEmpty($('#num_flojas').val(), 'No. hojas') ||
        isFieldEmpty($('#num_tomos').val(), 'No. tomos') ||
        isFieldEmpty($('#num_copias').val(), 'No. Copias') ||
        isFieldEmpty($('#asunto').val(), 'Asunto') ||
        isFieldEmpty($('#lugar').val(), 'Lugar') ||
        isFieldEmpty($('#id_cat_area').val(), 'Área') ||
        isFieldEmpty($('#id_usuario_area').val(), 'Usuario') ||
        isFieldEmpty($('#id_usuario_enlace').val(), 'Enlace') ||
        isFieldEmpty($('#id_cat_unidad').val(), 'Unidad') ||
        isFieldEmpty($('#id_cat_coordinacion').val(), 'Coordinación') ||
        isFieldEmpty($('#id_cat_estatus').val(), 'Estatus') ||
        isFieldEmpty($('#id_cat_tramite').val(), 'Tramite') ||
        isFieldEmpty($('#id_cat_clave').val(), 'Clave') ||
        isFieldEmpty($('#id_cat_remitente').val(), 'Remitente') ||
        isExceedingLength($('#num_documento').val(), 'No. Documento', 50) ||
        isExceedingLength($('#lugar').val(), 'Lugar', 100) ||
        isExceedingLength($('#asunto').val(), 'Asunto', 100) ||
        isExceedingLength($('#observaciones').val(), 'Observaciones', 100)*/false) {
        event.preventDefault();  // Evita el envío del formulario
        return;  // Detener la ejecución aquí
    } else {
        $('#num_documento_area').prop('disabled', false); //desabilitar contenido
    }
});