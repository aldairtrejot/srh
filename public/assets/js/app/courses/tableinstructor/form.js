// Obtener el token CSRF desde la metaetiqueta
const token = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function () {
    llenarDatosInstructor();
});

function llenarDatosInstructor() {
    const isEditing = $('#is_editing').val() === "1";

    if (isEditing) {
        console.log("🔄 Modo Edición: Cargando datos del instructor...");

        // Obtener los valores desde los inputs ocultos
        let nombre = $('#nombre').val()?.trim() || '';
        let primer_apellido = $('#primer_apellido').val()?.trim() || '';
        let segundo_apellido = $('#segundo_apellido').val()?.trim() || '';
        let rfc = $('#rfc').val()?.trim() || '';
        let curp = $('#curp').val()?.trim() || '';

        // Si los valores están vacíos, coloca un placeholder "_"
        nombre = nombre || '_';
        primer_apellido = primer_apellido || '_';
        segundo_apellido = segundo_apellido || '_';
        rfc = rfc || '_';

        // Insertar valores en la interfaz
        $('#label_nombre').text(nombre);
        $('#label_primer_apellido').text(primer_apellido);
        $('#label_segundo_apellido').text(segundo_apellido);
        $('#label_rfc').text(rfc);
        $('#curp').val(curp);

        console.log(`🔍 Datos cargados: ${nombre} ${primer_apellido} ${segundo_apellido} ${rfc} ${curp}`);
    }
}

function limpiarValores() {
    $('#label_nombre, #label_primer_apellido, #label_segundo_apellido, #label_rfc').text('_');
}

function validarcurp() {
    let curp = $('#curp').val().trim();

    if (curp.length !== 18) {
        alert("La CURP debe tener 18 caracteres.");
        return;
    }

    $.ajax({
        url: URL_DEFAULT.concat('/tableinstructor/table/dataCurp'),
        type: 'POST',
        data: { curp: curp },
        success: function (response) {
            if (response.status && response.value) {
                $('#label_nombre').text(response.value.nombre || '_');
                $('#label_primer_apellido').text(response.value.primer_apellido || '_');
                $('#label_segundo_apellido').text(response.value.segundo_apellido || '_');
                $('#label_rfc').text(response.value.rfc || '_');
            } else {
                alert(response.message || 'No se encontraron datos.');
                limpiarValores();
            }
        },
        error: function () {
            alert("Error en la consulta de CURP.");
            limpiarValores();
        }
    });
}

$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': token }
});

$('#form-instructor').on('submit', function (event) {
    event.preventDefault();

    let formData = $(this).serializeArray();
    let instructorId = $('#id_tbl_instructores').val();
    let isEditing = $('#is_editing').val() === "1";

    if (instructorId) {
        formData.push({ name: "id_tbl_instructores", value: instructorId });
    }

    let estatus = $('#estatus').is(':checked') ? "1" : "0";
    formData = formData.filter(item => item.name !== "estatus");
    formData.push({ name: "estatus", value: estatus });

    console.log("📤 Datos enviados:", formData);

    let requestType = isEditing ? 'POST' : 'POST';
    let requestData = $.param(formData);

    if (isEditing) {
        requestData += '&_method=PUT';
    }

    $.ajax({
        url: $(this).attr('action'),
        type: requestType,
        data: requestData,
        success: function () {
            let message = isEditing ? "✅ Instructor actualizado correctamente." : "✅ Instructor agregado correctamente.";
            alert(message);
            window.location.href = URL_DEFAULT.concat('/tableinstructor/list');
        },
        error: function () {
            let errorMessage = isEditing ? "❌ Error al actualizar el instructor." : "❌ Error al agregar el instructor.";
            alert(errorMessage);
        }
    });
});
