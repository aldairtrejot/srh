// Validacion de fórmulario

document.getElementById("myForm").addEventListener("submit", function (event) {
    let fecha_inicio = document.getElementById('fecha_inicio').value;
    let fecha_fin = document.getElementById('fecha_fin').value;

    let bool_user_role = $('#bool_user_role').val(); // Se obtienen los roles de usuario
    let new_variable = (bool_user_role && bool_user_role.trim() !== '') ? true : false;

    // Helper para selects (usa el mismo criterio en todos)
    const isSelectEmpty = (selector, label) => {
        const val = getVal(selector); // asumiendo que ya existe getVal()
        if (val === '' || val === null || typeof val === 'undefined') {
            notyfEM.error(`El campo ${label} es obligatorio.`);
            return true;
        }
        return false;
    };

    if (new_variable) {
        // Requeridos base (los que ya tenías)
        if (
            isFieldEmpty($('#num_documento').val(), 'No. Documento') ||
            isFieldEmpty($('#fecha_inicio').val(), 'Fecha de inicio') ||
            isFieldEmpty($('#fecha_fin').val(), 'Fecha fin') ||
            isFieldEmpty($('#folio_gestion').val(), 'Folio de gestión') ||
            isFieldEmpty($('#fecha_documento').val(), 'Fecha de doc.') ||
            isFieldEmpty($('#id_cat_entidad').val(), 'Entidad') ||
            isFieldEmpty($('#asunto').val(), 'Asunto') ||
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
            isExceedingLength($('#observaciones').val(), 'Observaciones', 140)
        ) {
            event.preventDefault();
            return;
        }

        // === REQUERIDOS: ÁREAS ===
        // Usa SIEMPRE getVal para los 3
        if (
            isSelectEmpty('#id_cat_area_1', 'Área 1') ||
            isSelectEmpty('#id_cat_area_2', 'Área 2') ||
            isSelectEmpty('#id_cat_area',   'Área 3')
        ) {
            event.preventDefault();
            return;
        }

        // Varios remitentes
        if ($('#son_mas_remitentes').val()) {
            if (isFieldEmpty($('#remitente').val(), 'Remitente') ||
                isExceedingLength($('#remitente').val(), 'Remitente', 230)) {
                event.preventDefault();
                return;
            }
        }

        // Remitente único seleccionado
        if (!$('#son_mas_remitentes').val() && !$('#rfc_remitente_bool').val()) {
            if (isFieldEmpty($('#id_cat_remitente').val(), 'Remitente')) {
                event.preventDefault();
                return;
            }
        }

        // Alta de remitente (RFC)
        if ($('#rfc_remitente_bool').val()) {
            if (isFieldEmpty($('#remitente_nombre').val(), 'Nombre') ||
                isFieldEmpty($('#remitente_apellido_paterno').val(), 'Apellido paterno') ||
                isFieldEmpty($('#remitente_apellido_materno').val(), 'Apellido materno') ||
                isExceedingLength($('#remitente_nombre').val(), 'Nombre', 50) ||
                isExceedingLength($('#remitente_apellido_paterno').val(), 'Apellido paterno', 50) ||
                isExceedingLength($('#remitente_apellido_materno').val(), 'Apellido materno', 50) ||
                isExceedingLength($('#remitente_rfc').val(), 'RFC', 13)) {
                event.preventDefault();
                return;
            }

            if ($('#remitente_rfc').val() !== '') {
                if (!validateRfc($('#remitente_rfc').val())) {
                    notyfEM.error('El RFC de remitente no es válido.');
                    event.preventDefault();
                    return;
                }
            }

            let isValidN = getUniqueNameRemitente($('#remitente_nombre').val(), $('#remitente_apellido_paterno').val(), $('#remitente_apellido_materno').val(), 'nombre');
            if (isValidN) {
                notyfEM.error('El Nombre de remitente ya está registrado.');
                event.preventDefault();
                return;
            }

            let isValidR = getUniqueRemitente($('#remitente_rfc').val(), 'rfc');
            if (isValidR) {
                notyfEM.error('El RFC de remitente ya está registrado.');
                event.preventDefault();
                return;
            }
        }

        // Horas si fechas iguales
        if (fecha_inicio == fecha_fin) {
            if (isFieldEmpty($('#horas_respuesta').val(), 'Horas respuesta')) {
                event.preventDefault();
                return;
            }
        }

        // Fechas coherentes
        if (fecha_inicio > fecha_fin) {
            notyfEM.error("La fecha de inicio no puede ser mayor a la fecha de fin.");
            event.preventDefault();
            return;
        }

        // Folio de gestión único (si lo quieres activo)
        let isValidG = getNoUnique($('#id_tbl_correspondencia').val(), $('#folio_gestion').val(), 'folio_gestion');
        if (isValidG) {
            notyfEM.error('El Folio de gestión ya está registrado.');
            event.preventDefault();
            return;
        }

    } else {
        // Role not administration
        if ($('#id_cat_estatus').val() == 7) {
            notyfEM.error('El usuario no tiene permisos para acceder a esta sección.');
            event.preventDefault();
            return;
        }

        if (
            isFieldEmpty($('#id_cat_estatus').val(), 'Estatus') ||
            isExceedingLength($('#observaciones').val(), 'Observaciones', 140)
        ) {
            event.preventDefault();
            return;
        }
    }

    // Rehabilitar campos antes de enviar
    $('#id_cat_estatus').prop('disabled', false);
    $('#id_cat_area').prop('disabled', false);
    $('#id_cat_area_1').prop('disabled', false);
    $('#id_cat_area_2').prop('disabled', false);
    $('#num_documento').prop('disabled', false);
    $('#folio_gestion').prop('disabled', false);
    $('#asunto').prop('disabled', false);
    $('#observaciones').prop('disabled', false);
});

// Validación cuando cambia fecha
$('#fecha_inicio').change(function () { validateDate(); });
$('#fecha_fin').change(function () { validateDate(); });

// La función valida que la fecha de inicio no sea mayor a la fecha de fin
function validateDate() {
    let fecha_inicio = document.getElementById('fecha_inicio').value;
    let fecha_fin = document.getElementById('fecha_fin').value;
    if (fecha_inicio !== '' && fecha_fin !== '') {
        if (fecha_inicio > fecha_fin) {
            notyfEM.error("La fecha de inicio no puede ser mayor a la fecha de fin.");
        }
    }
}

