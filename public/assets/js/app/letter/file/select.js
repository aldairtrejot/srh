
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
                name: 'correspondencia.rel_consecutivo_expedientes',
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
    $('#id_cat_area').val(idValue);// Asignarle valor
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
                foreachSelectNull(response.selectEnlace, '#id_usuario_enlace');
                foreachSelectNull(response.selectUsuario, '#id_usuario_area');

            },
        });
    } else {
        cleanSelectMoreSelect('#id_usuario_area'); //Se limpia el select
        cleanSelectMoreSelect('#id_usuario_enlace'); //Se limpia el select
    }
});

// se establece el valor en la variable
$('#id_usuario_area').on('change', function () {
    $('#id_usuario_area').val($(this).val());// Asignarle valor
});

// se establece el valor en la variable
$('#id_usuario_enlace').on('change', function () {
    $('#id_usuario_enlace').val($(this).val());// Asignarle valor
});