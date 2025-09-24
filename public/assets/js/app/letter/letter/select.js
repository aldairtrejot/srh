//Codigo para la seleccion de area y como cambia el valor de los demas select que dependen de ella
$('#id_cat_area').on('change', function () {
    let idValue = $(this).val();
    if (idValue) {
        $.ajax({
            url: URL_DEFAULT.concat('/letter/collection/collectionArea'),
            type: 'POST',
            data: { id: idValue, _token: token },
            success: function (response) {
                foreachSelectNull(response.selectEnlace, '#id_usuario_enlace');
                foreachSelectNull(response.selectUsuario, '#id_usuario_area');
                foreachSelectNull(response.selectUnidad, '#id_cat_unidad');
                foreachSelectNull(response.selectCoor, '#id_cat_coordinacion');
                foreachSelect(response.selectTramite, '#id_cat_tramite');

                cleanSelectMoreSelect('#id_cat_clave');
                clearClaveData();
                setClaveInNuSystem(response.clave);
            },
        });
    } else {
        cleanSelectMoreSelect('#id_usuario_area');
        cleanSelectMoreSelect('#id_usuario_enlace');
        cleanSelectMoreSelect('#id_cat_tramite');
        cleanSelectMoreSelect('#id_cat_unidad');
        cleanSelectMoreSelect('#id_cat_coordinacion');
        cleanSelectMoreSelect('#id_cat_clave');
        clearClaveData();
        setClaveInNuSystem('-');
    }
});

// Unidad -> Coordinación
$('#id_cat_unidad').on('change', function () {
    let idValue = $(this).val();
    if (idValue) {
        $.ajax({
            url: URL_DEFAULT.concat('/letter/collection/collectionUnidad'),
            type: 'POST',
            data: { id: idValue, _token: token },
            success: function (response) {
                foreachSelectNull(response.selectCoordinacion, '#id_cat_coordinacion');
            },
        });
    } else {
        cleanSelectMoreSelect('#id_cat_coordinacion');
    }
});

// Trámite -> Clave
$('#id_cat_tramite').on('change', function () {
    let idValue = $(this).val();
    if (idValue) {
        $.ajax({
            url: URL_DEFAULT.concat('/letter/collection/collectionTramite'),
            type: 'POST',
            data: { id: idValue, _token: token },
            success: function (response) {
                foreachSelectNull(response.selectClave, '#id_cat_clave');
            },
        });
    } else {
        cleanSelectMoreSelect('#id_cat_clave');
        clearClaveData();
    }
});

// Clave -> Encabezado
$('#id_cat_clave').on('change', function () {
    let idValue = $(this).val();
    if (idValue) {
        $.ajax({
            url: URL_DEFAULT.concat('/letter/collection/collectionClave'),
            type: 'POST',
            data: { id: idValue, _token: token },
            success: function (response) {
                let valueClave = response.valueOfClave;
                $('#_labClave').text(valueClave._labClave);
                $('#_labClaveCodigo').text(valueClave._labClaveCodigo);
                $('#_labClaveRedaccion').text(valueClave._labClaveRedaccion);
            },
        });
    } else {
        clearClaveData();
    }
});

// Helpers encabezado
function clearClaveData() {
    $('#_labClave').text('_');
    $('#_labClaveCodigo').text('_');
    $('#_labClaveRedaccion').text('_');
}

function setClaveInNuSystem(value) {
    let num_turno_sistema = $('#num_turno_sistema').val();
    let result = num_turno_sistema.replace(/^[^/]+/, value);
    $('#num_turno_sistema').val(result);
    $('#_labNoCorrespondencia').text(result);
}
