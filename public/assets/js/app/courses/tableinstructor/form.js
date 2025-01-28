    // Obtener el token CSRF desde la metaetiqueta
    const token = $('meta[name="csrf-token"]').attr('content');

    // Configurar el token CSRF para todas las solicitudes AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': token
        }
    });

    // Función para validar la CURP
    function validarcurp() {
        const curp = $('#curp').val().trim();
    
        // Validar que el campo CURP no esté vacío
        if (!curp) {
            notyfEM.error('Por favor, ingresa una CURP.');
            return;
        }
    
        // Expresión regular para validar el formato de la CURP
        const curpRegex = /^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]{2}$/i;
        if (!curpRegex.test(curp)) {
            notyfEM.error('El formato de CURP no es válido.');
            return;
        }
    
        // Realizar la solicitud AJAX para consultar el CURP
        $.ajax({
            url: URL_DEFAULT.concat('/tableinstructor/table/dataCurp'),
            type: 'POST',
            data: {
                curp: curp,
                _token: token // Usar el token extraído de la metaetiqueta
            },
            success: function (response) {
                let item = response.value; // Obtener los datos de la respuesta
    console.log(response);
                if (item) {
                    // Actualizar los campos del DOM con los valores recibidos
                    $('#remitente_nombre').text(item._nombre);
                    $('#remitente_primer_apellido').text(item._primer_apellido);
                    $('#remitente_segundo_apellido').text(item._segundo_apellido);
                    $('#remitente_rfc').text(item._rfc || 'N/A');
                    $('#id_cat_tipo_schema').val(item.id_cat_tipo_schema);
                    $('#id_tbl_empleados_hraes').val(item.id_tbl_empleados_hraes);
    
                    // Mostrar mensaje de éxito
                    notyfEM.success("CURP localizado con éxito.");
                } else {
                    // Mostrar mensaje de error si no se encontraron datos
                    notyfEM.error("No se encontraron datos para la CURP proporcionada.");
                    limpiarValores();
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al consultar la CURP:", error);
                notyfEM.error("Ocurrió un error al realizar la consulta. Por favor, inténtalo de nuevo.");
                limpiarValores();
            }
        });
    }
    
    // Función para limpiar los valores de los campos de resultados
    function limpiarValores() {
        $('#remitente_nombre').text('N/A');
        $('#remitente_primer_apellido').text('N/A');
        $('#remitente_segundo_apellido').text('N/A');
        $('#remitente_rfc').text('N/A');
    }
    