
//Codigo para la seleccion de area y como cambia el valor de los demas select que dependen de ella
$('#id_cat_area').on('change', function () {
    let idValue = $(this).val();  // Obtiene el valor de la opción seleccionada
    if (idValue) { // Realiza la solicitud AJAX solo si se ha seleccionado un valor
        $.ajax({
            url: '/srh/public/letter/collection/collectionArea',
            type: 'POST',
            data: {
                id: idValue,
                _token: token  // Usar el token extraído de la metaetiqueta
            },
            success: function (response) {
                //proceso de select 
                foreachSelect(response.selectEnlace, '#id_usuario_enlace');
                foreachSelect(response.selectUsuario, '#id_usuario_area');
            },
        });
    } else {
        cleanSelect('#id_usuario_area'); //Se limpia el select
        cleanSelect('#id_usuario_enlace'); //Se limpia el select
    }
});
