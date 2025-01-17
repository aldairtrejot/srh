
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

//Codigo para la seleccion de area y como cambia el valor de los demas select que dependen de ella
$('#id_cat_area_documento').on('change', function () {
    let idValue = $(this).val();  // Obtiene el valor de la opción seleccionada
    if (idValue) { // Realiza la solicitud AJAX solo si se ha seleccionado un valor
        $.ajax({
            url: URL_DEFAULT.concat('/letter/collection/collectionArea'),
            type: 'POST',
            data: {
                id: idValue,
                _token: token  // Usar el token extraído de la metaetiqueta
            },
            success: function (response) {
                //proceso de select 
                foreachSelectNull(response.selectEnlace, '#id_usuario_enlace_aux');
                foreachSelectNull(response.selectUsuario, '#id_usuario_area_aux');

            },
        });
    } else {
        cleanSelectMoreSelect('#id_usuario_area_aux'); //Se limpia el select
        cleanSelectMoreSelect('#id_usuario_enlace_aux'); //Se limpia el select
    }
});