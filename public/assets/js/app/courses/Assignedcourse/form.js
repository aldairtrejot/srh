// Obtener el token CSRF desde la metaetiqueta
const token = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function () {
    llenarDatosAlumnos();
});

function llenarDatosAlumnos() {
    const isEditing = $('#is_editing').val() === "1";

    if (isEditing) {
        console.log("🔄 Modo Edición: Cargando datos del Alumno...");

        // Obtener los valores desde los inputs ocultos
        let nombre = $('#nombre').val()?.trim() || '';
        let primer_apellido = $('#primer_apellido').val()?.trim() || '';
        let segundo_apellido = $('#segundo_apellido').val()?.trim() || '';
        let rfc = $('#rfc').val()?.trim() || '';
        let curp = $('#curp').val()?.trim() || '';

        // Si los valores están vacíos, coloca un placeholder "_"
        nombre = nombre || '';
        primer_apellido = primer_apellido || '';
        segundo_apellido = segundo_apellido || '';
        rfc = rfc || '';

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
    $('#id_cursos').val(''); // 🔹 También limpiamos el curso seleccionado
}

function validarcurp() {
    let curp = $('#curp').val().trim();

    if (curp.length !== 18) {
        alert("⚠️ La CURP debe tener 18 caracteres.");
        return;
    }

    $.ajax({
        url: `${window.location.origin}/srh/public/assignedcourse/dataCurp`,
        type: 'POST',
        data: { curp: curp },
        success: function (response) {
            if (response.status && response.value) {
                $('#label_nombre').text(response.value.nombre || '_');
                $('#label_primer_apellido').text(response.value.primer_apellido || '_');
                $('#label_segundo_apellido').text(response.value.segundo_apellido || '_');
                $('#label_rfc').text(response.value.rfc || '_');
            } else {
                alert("⚠️ No se encontraron datos para la CURP ingresada.");
                limpiarValores(); // 🔹 Limpia valores cuando no hay datos
            }
        },
        error: function () {
            alert("❌ Error en la consulta de CURP.");
            limpiarValores(); // 🔹 Limpia valores en caso de error
        }
    });
}

$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': token }
});

$('#form-assignedcourse').on('submit', function (event) {
    event.preventDefault();

    let formData = $(this).serializeArray();
    let alumnoId = $('#id_usuarios').val();
    let isEditing = $('#is_editing').val() === "1";
    let idCurso = $('#id_cursos').val(); // 🔹 Obtener el curso seleccionado

    if (alumnoId) {
        formData.push({ name: "id_usuarios", value: alumnoId });
    }

    let estatus = $('#estatus').is(':checked') ? "1" : "0";
    formData = formData.filter(item => item.name !== "estatus");
    formData.push({ name: "estatus", value: estatus });

    // 🔹 Evitar enviar id_cursos si no se ha seleccionado
    if (!idCurso || idCurso === "") {
        console.log("⚠️ No se seleccionó ningún curso. No se enviará id_cursos.");
        formData = formData.filter(item => item.name !== "id_cursos");
    }

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
        success: function (response) {
            alert(response.message); // ✅ obtenemos el mensaje del backend
            window.location.href = URL_DEFAULT.concat('/assignedcourse/list');
        },
        error: function () {
            alert(errorMessage);
        }
    });
});
