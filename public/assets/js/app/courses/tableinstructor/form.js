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
        let nombre = $('#nombre').val()?.trim() || '_';
        let primer_apellido = $('#primer_apellido').val()?.trim() || '_';
        let segundo_apellido = $('#segundo_apellido').val()?.trim() || '_';
        let rfc = $('#rfc').val()?.trim() || '_';
        let curp = $('#curp').val()?.trim() || '';

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
        headers: {
            'X-CSRF-TOKEN': token
        },
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
