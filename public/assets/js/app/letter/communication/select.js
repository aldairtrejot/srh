
// Evento de activacion de catalogo de areas 
$('#id_cat_area_interno').on('change', function () {
    let idValue = $(this).val();  // Obtiene el valor de la opción seleccionada
    $('#_labNomArea').text(' _');
    if (idValue) { // Realiza la solicitud AJAX solo si se ha seleccionado un valor
        $.ajax({
            url: URL_DEFAULT.concat('/communication/area'),
            type: 'POST',
            data: {
                id: idValue,
                _token: token  // Usar el token extraído de la metaetiqueta
            },
            success: function (response) {
                let item = response;
                $('#_labNomArea').text(item.result); // Asignar valores a labelput
            },
        });
    }
});
