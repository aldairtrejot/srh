
//Codigo para la seleccion de area y como cambia el valor de los demas select que dependen de ella
$('#id_cat_area').on('change', function () {
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
                foreachSelectNull(response.selectEnlace, '#id_usuario_enlace');
                foreachSelectNull(response.selectUsuario, '#id_usuario_area');
                foreachSelectNull(response.selectUnidad, '#id_cat_unidad');
                foreachSelectNull(response.selectCoor, '#id_cat_coordinacion');
                foreachSelect(response.selectTramite, '#id_cat_tramite');

                cleanSelectMoreSelect('#id_cat_clave'); //Se limpia el select
                clearClaveData(); //Limpieza de encabezado
                setClaveInNuSystem(response.clave); // modificación de no de correspondencia
            },
        });
    } else {
        cleanSelectMoreSelect('#id_usuario_area'); //Se limpia el select
        cleanSelectMoreSelect('#id_usuario_enlace'); //Se limpia el select
        cleanSelectMoreSelect('#id_cat_tramite'); //Se limpia el select
        cleanSelectMoreSelect('#id_cat_unidad'); //Se limpia el select
        cleanSelectMoreSelect('#id_cat_coordinacion'); //Se limpia el select
        cleanSelectMoreSelect('#id_cat_clave'); //Se limpia el select
        clearClaveData(); //Limpieza de encabezado
        setClaveInNuSystem('-'); // modificación de no de correspondencia
    }
});


//Codigo para la seleccion de area y como cambia el valor de los demas select que dependen de ella
$('#id_cat_unidad').on('change', function () {
    let idValue = $(this).val();  // Obtiene el valor de la opción seleccionada
    if (idValue) { // Realiza la solicitud AJAX solo si se ha seleccionado un valor
        $.ajax({
            url: URL_DEFAULT.concat('/letter/collection/collectionUnidad'),
            type: 'POST',
            data: {
                id: idValue,
                _token: token  // Usar el token extraído de la metaetiqueta
            },
            success: function (response) {

                //Proceso de select
                foreachSelectNull(response.selectCoordinacion, '#id_cat_coordinacion');
            },
        });
    } else {
        cleanSelectMoreSelect('#id_cat_coordinacion'); //Se limpia el select
    }
});

//Codigo para la seleccion de Trmaite y como cambia el valor de los demas select que dependen de ella
$('#id_cat_tramite').on('change', function () {
    let idValue = $(this).val();  // Obtiene el valor de la opción seleccionada
    if (idValue) { // Realiza la solicitud AJAX solo si se ha seleccionado un valor
        $.ajax({
            url: URL_DEFAULT.concat('/letter/collection/collectionTramite'),
            type: 'POST',
            data: {
                id: idValue,
                _token: token  // Usar el token extraído de la metaetiqueta
            },
            success: function (response) {
                //Proceso de select
                foreachSelectNull(response.selectClave, '#id_cat_clave');
            },
        });
    } else {
        cleanSelectMoreSelect('#id_cat_clave'); //Se limpia el select
        clearClaveData(); //Limpieza de encabezado
    }
});


//Codigo que al momento de seleccionar la clave, cambian los valores
$('#id_cat_clave').on('change', function () {
    let idValue = $(this).val();  // Obtiene el valor de la opción seleccionada
    if (idValue) { // Realiza la solicitud AJAX solo si se ha seleccionado un valor
        $.ajax({
            url: URL_DEFAULT.concat('/letter/collection/collectionClave'),
            type: 'POST',
            data: {
                id: idValue,
                _token: token  // Usar el token extraído de la metaetiqueta
            },
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

//Limpiar los valores de clave que aparecen en el encabezado
function clearClaveData() {
    $('#_labClave').text('_');
    $('#_labClaveCodigo').text('_');
    $('#_labClaveRedaccion').text('_');
}

// La funcion estable el nuevo no de sistema por el area o sin el area
function setClaveInNuSystem(value) {
    let num_turno_sistema = $('#num_turno_sistema').val();
    let result = num_turno_sistema.replace(/^[^/]+/, value);

    // Se establen los resultados en la variables
    $('#num_turno_sistema').val(result);
    $('#_labNoCorrespondencia').text(result);
}
