
// La funcion valida que el no de correspondecia este asociado a correspondencia
function getNoCorrespondencia(value) {
    var isValid = false;  // Asumimos que es inválido inicialmente

    $.ajax({
        url: '/srh/public/collection/validate/letter',
        type: 'POST',
        async: false, // Asegura que la ejecución sea sincrónica
        data: {
            value: value,
            _token: token  // Usar el token extraído de la metaetiqueta
        },
        success: function (response) {
            let item = response.status;
            if (item) {
                isValid = true;  // La validación fue exitosa
            }
        }
    });

    return isValid;  // Regresa el resultado de la validación
}