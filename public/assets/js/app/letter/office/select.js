
// Evento de activacion de catalogo de areas por incremento
$('#id_cat_area_documento').on('change', function () {
    let idValue = $(this).val();  // Obtiene el valor de la opción seleccionada
    $('#num_documento_area').val('');// Limpiar input
    if (idValue) { // Realiza la solicitud AJAX solo si se ha seleccionado un valor
        $.ajax({
            url: URL_DEFAULT.concat('/collection/area/consecutivo'),
            type: 'POST',
            data: {
                id: idValue,
                id_cat_anio: $('#id_cat_anio').val(),
                _token: token  // Usar el token extraído de la metaetiqueta
            },
            success: function (response) {
                let item = response;
                $('#num_documento_area').val(item.consecutivo);// Asignarle valor
            },
        });
    }
});
