
//Codigo para la seleccion de area y como cambia el valor de los demas select que dependen de ella
$('#id_cat_dependencia').on('change', function () {
    let idValue = $(this).val();  // Obtiene el valor de la opción seleccionada
    if (idValue) { // Realiza la solicitud AJAX solo si se ha seleccionado un valor
        $.ajax({
            url: URL_DEFAULT.concat('/external/collection/area'),
            type: 'POST',
            data: {
                id: idValue,
                _token: token  // Usar el token extraído de la metaetiqueta
            },
            success: function (response) {
                //proceso de select 
                foreachSelectNull(response.collectionArea, '#id_cat_dependencia_area');
            },
        });
    } else {
        cleanSelectMoreSelect('#id_cat_dependencia_area'); //Se limpia el select
    }
});
